<?php

namespace App\Controller;

use App\Form\SearchPostType;
use App\Repository\CategoryRepository;
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
    public function index(Request $request, bool $isMac, HttpKernelInterface $httpKernel, PostRepository $postRepository, LoggerInterface $logger, CategoryRepository $categoryRepository): Response
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

        /////////////////////////
        // RECHERCHE VERSION 1 //
        /////////////////////////


//        $posts = null;
//
//        if ($request->request->count()) {
//
//            $createdAtMin = $request->request->get('created-at-min');
//            $categoryId = $request->request->get('category');
//
//            $posts = $postRepository->search($categoryId, $createdAtMin);
//        }
//
//        if ($posts === null) {
//            $posts = $postRepository->findBy([], ['createdAt' => 'DESC']);
//        }

        /////////////////////////
        // RECHERCHE VERSION 2 //
        /////////////////////////

        $form = $this->createForm(SearchPostType::class);
        $form->handleRequest($request);

        $category = null;
        $createdAtMin = null;

        if ($form->isSubmitted() && $form->isValid()) {
            $category = $form->get('category')->getData();
            $createdAtMin = $form->get('created_at_min')->getData();
        }

        $posts = $postRepository->search($category, $createdAtMin);

        return $this->render('home/index.html.twig', [
            'posts' => $posts,
            'isMac' => $isMac,
            // 'categories' => $categoryRepository->findBy([], ['name' => 'ASC']) // Pour la version 1
            'form' => $form->createView()
        ]);
    }
}
