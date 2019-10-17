<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class HelloController extends AbstractController
{

    public function index(Environment $twig)
    {
        $content = null;
        $param = $this->getParameter('param_test');
        try {
            $content = $twig->render('Hello/index.html.twig', ['name' => $param]);
        } catch (LoaderError $e) {
        } catch (RuntimeError $e) {
        } catch (SyntaxError $e) {
        }

        return new Response($content);
    }
}