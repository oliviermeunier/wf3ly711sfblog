<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\CommentType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    /**
     * @Route("/post/{slug}", name="post.index")
     */
    public function index($slug, Post $post, Request $request, EntityManagerInterface $manager /*$slug, PostRepository $postRepository*/): Response
    {
        //$post = $postRepository->findOneBy(['slug' => $slug]);

        // Création de l'objet CommentType (formulaire)
        $form = $this->createForm(CommentType::class);

        // Injection des données de la requête HTTP (du formulaire) dans l'objet CommentType
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide (correctement rempli)
        if ($form->isSubmitted() && $form->isValid()) {

            // Récupération d'une entité Comment à partir des données du formulaire et customisation
            $comment = $form->getData();
            $comment->setPost($post);
            $comment->setUser($this->getUser());

            // On persiste l'entité avec le manager
            $manager->persist($comment);
            $manager->flush();

            // Message falsh
            $this->addFlash('success', 'Votre commentaire a bien été ajouté.');

            // Redirection pour vider le corps de la requête (données du formulaire)
            // return $this->redirectToRoute('post.index', ['slug' => $slug]);
            return $this->redirect($request->headers->get('referer'));
        }

        return $this->render('post/index.html.twig', [
            'post' => $post,
            'form' => $form->createView()
        ]);
    }
}
