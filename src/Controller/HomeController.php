<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home.index")
     */
    public function index(bool $isMac, HttpKernelInterface $httpKernel, PostRepository $postRepository, LoggerInterface $logger): Response
    {
//        $logger->info('I\'m the controller !');

//        dd($isMac);

//        $subRequest = new Request();
//
//        $subRequest->attributes->set('_controller', 'App\\Controller\\PartialController::sayHello');
//        $subRequest->server->set('REMOTE_ADDR', '127.0.0.1');
//
//        $response = $httpKernel->handle(
//            $subRequest,
//            HttpKernelInterface::SUB_REQUEST
//        );

        $posts = $postRepository->findBy([], ['createdAt' => 'DESC']);

        return $this->render('home/index.html.twig', [
            'posts' => $posts,
            'isMac' => $isMac
        ]);
    }
}
