<?php


namespace App\Controller;


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
class AdvertController extends AbstractController
{
    public static function advertsMock():array {
        return array(
            array(
                'title'   => 'Recherche développpeur Symfony',
                'id'      => 1,
                'author'  => 'Alexandre',
                'content' => 'Nous recherchons un développeur Symfony débutant sur Lyon. Blabla…',
                'date'    => new \Datetime()),
            array(
                'title'   => 'Mission de webmaster',
                'id'      => 2,
                'author'  => 'Hugo',
                'content' => 'Nous recherchons un webmaster capable de maintenir notre site internet. Blabla…',
                'date'    => new \Datetime()),
            array(
                'title'   => 'Offre de stage webdesigner',
                'id'      => 3,
                'author'  => 'Mathieu',
                'content' => 'Nous proposons un poste pour webdesigner. Blabla…',
                'date'    => new \Datetime())
        );
    }

    /**
     * @Route("/{page}", name="oc_advert_index", requirements={"page" = "\d+"}, defaults={"page" = 1})
     *
     */
    public function index($page)
    {

        if ($page < 1) {
            throw  $this->createNotFoundException('Page ' . $page . ' inexistante.');
        }

        $listAdverts = self::advertsMock();

        return $this->render('Advert/index.html.twig', array(
            'listAdverts' => $listAdverts
        ));
    }

    /**
     * @Route("/view/{id}", name="oc_advert_view", requirements={"id" = "\d+"})
     */
    public function view($id)
    {

        // liste en dur
        $listAdverts = self::advertsMock();
        $aPost = null;
        foreach ($listAdverts as $element) {
            if ($element['id'] == $id) {
                $aPost = $element;
                break;
            }
        }
        return $this->render('Advert/view.html.twig', ['post' => $aPost]);
    }

    /**
     * @Route("/add", name="oc_advert_add")
     */
    public function add(Request $request)
    {
        if ($request->isMethod('POST')) {
            $this->addFlash('notice', 'Annonce bien enregistrée.');
            return $this->redirectToRoute('oc_advert_view', ['id' => 5]);
        }
        return $this->render('Advert/add.html.twig');
    }

    /**
     * @Route("/edit/{id}", name="oc_advert_edit", requirements={"id" = "\d+"})
     */
    public function edit($id, Request $request)
    {
        if ($request->isMethod('POST')) {
            $this->addFlash('notice', 'Annonce bien modifiée.');
            return $this->redirectToRoute('oc_advert_view', ['id' => 5]);
        }
        $listAdverts = self::advertsMock();
        $post = null;

        foreach ($listAdverts as $aPost){
            if($aPost['id'] == $id){
                $post = $aPost;
                break;
            }
        }
        return $this->render('Advert/edit.html.twig', ['post' => $post]);
    }

    /**
     * @Route("/delete/{id}", name="oc_advert_delete", requirements={"id" = "\d+"})
     */
    public function delete($id)
    {
        return $this->render('Advert/delete.html.twig');

    }

    public function menu($limit)
    {
        $listAdverts = array(
            array('id' => 2, 'title' => 'Menu item n°1'),
            array('id' => 5, 'title' => 'Menu item n°2'),
            array('id' => 9, 'title' => 'Menu item n°3')
        );

        return $this->render('Advert/menu.html.twig', array(
            'listAdverts' => $listAdverts
        ));
    }
}
