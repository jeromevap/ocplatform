<?php


namespace App\Controller;


use App\Entity\Advert;
use App\Form\AdvertEditType;
use App\Form\AdvertType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
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
        // On crée un objet Advert
        $advert = new Advert();

        $form = $this->get('form.factory')->create(AdvertType::class, $advert);

        // Si la requête est en POST
        if ($request->isMethod('POST')) {
            // On fait le lien Requête <-> Formulaire
            $form->handleRequest($request);

            // On vérifie que les valeurs entrées sont correctes
            if ($form->isValid()) {
                $advert->getImage()->upload();

                // On enregistre notre objet $advert dans la base de données, par exemple
                $em = $this->getDoctrine()->getManager();
                $em->persist($advert);
                $em->flush();

                $request->getSession()->getFlashBag()->add(
                  'notice',
                  'Annonce bien enregistrée.'
                );

                // On redirige vers la page de visualisation de l'annonce
                return $this->redirectToRoute(
                  'oc_advert_view',
                  ['id' => $advert->getId()]
                );
            }
        }
        // À ce stade, le formulaire n'est pas valide car :
        // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
        // - Soit la requête est de type POST, mais le formulaire contient des valeurs invalides, donc on l'affiche de nouveau
        return $this->render(
          'Advert/add.html.twig',
          [
            'form' => $form->createView(),
          ]
        );
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
        $manager = $this->getDoctrine()->getManager();
        $advert = $manager->getRepository('App:Advert')->find($id);
        if (is_null($advert)) {
            throw $this->createNotFoundException(
              "Aucune annonce d'ID ${id} trouvée dans la base."
            );
        }

        $form = $this->get('form.factory')->create(AdvertEditType::class, $advert);

        // Si la requête est en POST
        if ($request->isMethod('POST')) {
            // On fait le lien Requête <-> Formulaire
            $form->handleRequest($request);

            // On vérifie que les valeurs entrées sont correctes
            if ($form->isValid()) {
                // On enregistre notre objet $advert dans la base de données, par exemple
                $em = $this->getDoctrine()->getManager();
                $em->persist($advert);
                $em->flush();

                $request->getSession()->getFlashBag()->add(
                  'notice',
                  'Annonce bien enregistrée.'
                );

                // On redirige vers la page de visualisation de l'annonce
                return $this->redirectToRoute(
                  'oc_advert_view',
                  ['id' => $advert->getId()]
                );
            }
        }

        // La méthode findAll retourne toutes les catégories de la base de données
        $listCategories = $manager->getRepository('App:Category')->findAll();

        // On boucle sur les catégories pour les lier à l'annonce
        foreach ($listCategories as $category) {
            $advert->addCategory($category);
        }

        // Pour persister le changement dans la relation, il faut persister l'entité propriétaire
        // Ici, Advert est le propriétaire, donc inutile de la persister car on l'a récupérée depuis Doctrine

        // Étape 2 : On déclenche l'enregistrement
        $manager->flush();

        return $this->render(
          'Advert/edit.html.twig',
          [
            'form' => $form->createView(),
            'advert' => $advert,
          ]
        );
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
            throw $this->createNotFoundException(
              "L'annonce d'id ".$id." n'existe pas."
            );
        }

        // On boucle sur les catégories de l'annonce pour les supprimer
        foreach ($advert->getCategories() as $category) {
            $advert->removeCategory($category);
        }

        // Pour persister le changement dans la relation, il faut persister l'entité propriétaire
        // Ici, Advert est le propriétaire, donc inutile de la persister car on l'a récupérée depuis Doctrine

        // On déclenche la modification
        $em->flush();

        return $this->render('Advert/delete.html.twig');

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
    private function createFormFor(Advert $advert) : FormInterface
    {
        return $this->get('form.factory')->create(AdvertType::class, $advert);
    }

}
