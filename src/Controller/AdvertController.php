<?php


namespace App\Controller;


use App\Entity\Advert;
use App\Entity\Application;
use App\Entity\Image;
use Datetime;
use Doctrine\ORM\EntityNotFoundException;
use phpDocumentor\Reflection\DocBlock\Tags\Throws;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

/**
 * @Route("/advert")
 *
 * Route par défaut du controller
 */
class AdvertController extends AbstractController {

  public static function advertsMock(): array {
    return [
      [
        'title' => 'Recherche développpeur Symfony',
        'id' => 1,
        'author' => 'Alexandre',
        'content' => 'Nous recherchons un développeur Symfony débutant sur Lyon. Blabla…',
        'date' => new Datetime(),
      ],
      [
        'title' => 'Mission de webmaster',
        'id' => 2,
        'author' => 'Hugo',
        'content' => 'Nous recherchons un webmaster capable de maintenir notre site internet. Blabla…',
        'date' => new Datetime(),
      ],
      [
        'title' => 'Offre de stage webdesigner',
        'id' => 3,
        'author' => 'Mathieu',
        'content' => 'Nous proposons un poste pour webdesigner. Blabla…',
        'date' => new Datetime(),
      ],
    ];
  }

  /**
   * @Route("/{page}", name="oc_advert_index", requirements={"page" = "\d+"},
   *   defaults={"page" = 1})
   *
   */
  public function index($page) {

    if ($page < 1) {
      throw  $this->createNotFoundException('Page ' . $page . ' inexistante.');
    }

    $listAdverts = self::advertsMock();

    return $this->render('Advert/index.html.twig', [
      'listAdverts' => $listAdverts,
    ]);
  }

  /**
   * @Route("/view/{id}", name="oc_advert_view", requirements={"id" = "\d+"})
   */
  public function view($id) {

    $anAdvert = $this->getDoctrine()->getRepository('App:Advert')->find($id);
    if (is_null($anAdvert)) {
      throw new EntityNotFoundException("Annonce ${id} introuvable dans la base.");
    };

    $applications = $this->getDoctrine()
      ->getRepository('App:Application')
      ->findBy(array('advert' => $anAdvert));

    return $this->render('Advert/view.html.twig', array(
      'advert' => $anAdvert, 'applications' => $applications));
  }

  /**
   * @Route("/add", name="oc_advert_add")
   */
  public function add(Request $request) {
    //Récupération de doctrine du repository pour la classe Advert (un repo par classe) :
    $manager = $this->getDoctrine()->getManager();
    $anAdvert = new Advert('Mon titre d\'annonce',
      'Jérôme V.',
      'Un petit texte exemple pour cette annonce qui ne contient ' .
      'pas grand chose.');

    $anAdvert->setImage(
      new Image(
        'https://espresso-jobs.com/conseils-carriere/wp-content/uploads/2018/02/job-de-reve.jpg',
        'Job de rêve'
      )
    );

    $ap1 = new Application("Pierre Perret", "Je suis hyper motivé.");
    $ap2 = new Application("Janine Petit", "J'ai encore de l'énergie à revendre !");
    $ap1->setAdvert($anAdvert);
    $ap2->setAdvert($anAdvert);

    $manager->persist($anAdvert);
    $manager->persist($ap1);
    $manager->persist($ap2);

    $manager->flush();

    if ($request->isMethod('POST')) {
      $this->addFlash('notice', 'Annonce bien enregistrée.');
      return $this->redirectToRoute('oc_advert_view', ['id' => $anAdvert->getId()]);
    }
    return $this->render('Advert/add.html.twig');
  }

  /**
   * @Route("/edit/{id}", name="oc_advert_edit", requirements={"id" = "\d+"})
   */
  public function edit($id, Request $request) {
    if ($request->isMethod('POST')) {
      $this->addFlash('notice', 'Annonce bien modifiée.');
      return $this->redirectToRoute('oc_advert_view', ['id' => $id]);
    }
    $manager = $this->getDoctrine()->getManager();

    $advert = $manager->getRepository('App:Advert')->find($id);
    if (is_null($advert)) {
      throw new EntityNotFoundException("Aucune annonce d'ID ${id} trouvée dans la base.");
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

    return $this->render('Advert/edit.html.twig', ['advert' => $advert]);
  }

  /**
   * @Route("/delete/{id}", name="oc_advert_delete", requirements={"id" =
   *   "\d+"})
   */
  public function delete($id) {

    $em = $this->getDoctrine()->getManager();

    // On récupère l'annonce $id
    $advert = $em->getRepository('App:Advert')->find($id);

    if (is_null($advert)) {
      throw new EntityNotFoundException("L'annonce d'id ".$id." n'existe pas.");
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

  public function menu($limit) {
    $listAdverts = [
      ['id' => 2, 'title' => 'Menu item n°1'],
      ['id' => 5, 'title' => 'Menu item n°2'],
      ['id' => 9, 'title' => 'Menu item n°3'],
    ];

    return $this->render('Advert/menu.html.twig', [
      'listAdverts' => $listAdverts,
    ]);
  }

}
