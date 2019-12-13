<?php


namespace App\Controller;


use App\Entity\Advert;
use App\Form\AdvertEditType;
use App\Form\AdvertType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/advert")
 *
 * Route par défaut du controller
 */
class AdvertController extends AbstractController
{

    public const ADVERT_PER_PAGE = 3;


    /**
     * @Route("/{page}", name="oc_advert_index", requirements={"page" = "\d+"},
     *   defaults={"page" = 1})
     *
     */
    public function index($page)
    {

        if ($page < 1) {
            throw  $this->createNotFoundException(
              'Page '.$page.' inexistante.'
            );
        }

        $listAdverts = $this->getDoctrine()->getManager()->getRepository(
          'App:Advert'
        )->getAdverts($page, self::ADVERT_PER_PAGE);

        // On calcule le nombre total de pages grâce au count($listAdverts) qui retourne le nombre total d'annonces
        $nbPages = ceil(count($listAdverts) / self::ADVERT_PER_PAGE);

        // Si la page n'existe pas, on retourne une 404
        if ($page > $nbPages) {
            throw $this->createNotFoundException(
              "La page ".$page." n'existe pas."
            );
        }

        // On donne toutes les informations nécessaires à la vue
        return $this->render(
          'Advert/index.html.twig',
          [
            'listAdverts' => $listAdverts,
            'nbPages' => $nbPages,
            'page' => $page,
          ]
        );
    }

    /**
     * @todo Optimisation à faire. Une requête par Skill...
     *
     * @Route("/view/{id}", name="oc_advert_view", requirements={"id" = "\d+"})
     */
    public function view($id)
    {

        $anAdvert = $this->getDoctrine()
          ->getRepository('App:Advert')
          ->getAdvertById($id);
        if (is_null($anAdvert)) {
            throw $this->createNotFoundException(
              "Annonce ${id} introuvable dans la base."
            );
        };

        $applications = $this->getDoctrine()
          ->getRepository('App:Application')
          ->findBy(['advert' => $anAdvert]);

        $advertSkills = $this->getDoctrine()
          ->getRepository('App:AdvertSkill')
          ->findBy(['advert' => $anAdvert]);


        return $this->render(
          'Advert/view.html.twig',
          [
            'advert' => $anAdvert,
            'applications' => $applications,
            'advertSkills' => $advertSkills,
          ]
        );
    }

    /**
     * @Route("/add", name="oc_advert_add")
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function add(Request $request)
    {
        {
            $advert = new Advert();
            $form = $this->get('form.factory')->create(
              AdvertType::class,
              $advert
            );

            if ($request->isMethod('POST') && $form->handleRequest($request)
                ->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($advert);
                $em->flush();

                $request->getSession()->getFlashBag()->add(
                  'notice',
                  'Annonce bien enregistrée.'
                );

                return $this->redirectToRoute(
                  'oc_advert_view',
                  ['id' => $advert->getId()]
                );
            }

            return $this->render(
              'Advert/add.html.twig',
              [
                'form' => $form->createView(),
              ]
            );
        }
    }

    /**
     * @Route("/edit/{id}", name="oc_advert_edit", requirements={"id" = "\d+"})
     * @param $id
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function edit($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $advert = $em->getRepository('App:Advert')->find($id);

        if (null === $advert) {
            throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
        }

        $form = $this->get('form.factory')->create(AdvertEditType::class, $advert);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            // Inutile de persister ici, Doctrine connait déjà notre annonce
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Annonce bien modifiée.');

            return $this->redirectToRoute('oc_advert_view', array('id' => $advert->getId()));
        }

        return $this->render(':Advert:edit.html.twig', array(
          'advert' => $advert,
          'form'   => $form->createView(),
        ));
    }

    //@formatter:off
  /**
   * @Route("/delete/{id}", name="oc_advert_delete", requirements={"id" = "\d+"})
   */
  //@formatter:on
    public function delete($id)
    {

        $em = $this->getDoctrine()->getManager();

        // On récupère l'annonce $id
        $advert = $em->getRepository('App:Advert')->find($id);

        if (is_null($advert)) {
            throw new NotFoundHttpException(
              "L'annonce d'id ${id} n'existe pas."
            );
        }

        // On crée un formulaire vide, qui ne contiendra que le champ CSRF
        // Cela permet de protéger la suppression d'annonce contre cette faille
        $form = $this->get('form.factory')->create();

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $em->remove($advert);
            $em->flush();

            $request->getSession()->getFlashBag()->add('info', "L'annonce a bien été supprimée.");

            return $this->redirectToRoute('oc_advert_index');
        }

        return $this->render('Advert/delete.html.twig', array(
          'advert' => $advert,
          'form'   => $form->createView(),
        ));

    }

    // pas de route, l'appel se fait au sein du template global.
    public function menu($limit)
    {
        $qb = $this->getDoctrine()
          ->getRepository('App:Advert')
          ->createQueryBuilder('adv');
        $qb->addOrderBy('adv.date', 'DESC');
        $qb->setMaxResults($limit);
        $listAdverts = $qb->getQuery()->getResult();

        return $this->render(
          'Advert/menu.html.twig',
          [
            'listAdverts' => $listAdverts,
          ]
        );
    }

    /**
     * @Route("/application", name="oc_advert_application", path="/application")
     */
    public function application()
    {
        $rp = $this->getDoctrine()->getRepository('App:Application');

        $applications = $rp->getApplicationsWithAdvert(5);

        return $this->render(
          'Advert/application.html.twig',
          ['applications' => $applications]
        );
    }

    /**
     * @param \App\Entity\Advert $advert
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createFormFor(Advert $advert): FormInterface
    {
        return $this->get('form.factory')->create(AdvertType::class, $advert);
    }

}
