<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PartialController extends AbstractController
{
    /**
     * @Route("/partial/hello", name="partial.sayHello")
     */
    public function sayHello($isMac): Response
    {
        return $this->render('partial/hello.html.twig', [
            'isMac' => $isMac
        ]);
    }
}
