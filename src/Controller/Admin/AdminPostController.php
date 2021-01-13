<?php

namespace App\Controller\Admin;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use App\Service\UploaderHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class AdminPostController extends AbstractController
{
    /**
     * @var SluggerInterface
     */
    private $slugger;

    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * @var UploaderHelper
     */
    private $uploaderHelper;

    public function __construct(SluggerInterface $slugger, EntityManagerInterface $manager, UploaderHelper $uploaderHelper)
    {
        $this->slugger = $slugger;
        $this->manager = $manager;
        $this->uploaderHelper = $uploaderHelper;
    }

    /**
     * @Route("/admin/post/new", name="admin.post.new")
     */
    public function new(Request $request): Response
    {
        $form = $this->createForm(PostType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $post = $form->getData();

            // Gestion du slug
            $slug = $this->slugger->slug($post->getTitle());
            $post->setSlug($slug);

            // Gestion de l'utilisateur associé à l'article
            $post->setUser($this->getUser());

            // Gestion du fichier image : on utilise notre classe de service
            $this->uploaderHelper->uploadPostImage($post, $form->get('imageFile')->getData());

            // On persiste en BDD
            $this->manager->persist($post);
            $this->manager->flush();

            // Message flash
            $this->addFlash('success', 'Article ajouté.');

            // Redirection vers le dashboard admin
            return $this->redirectToRoute('admin.index');
        }

        return $this->render('admin/post/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/post/edit/{id<\d+>}", name="admin.post.edit")
     */
    public function edit(Post $post, Request $request)
    {
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $post = $form->getData();

            // Gestion du slug
            $slug = $this->slugger->slug($post->getTitle());
            $post->setSlug($slug);

            // Gestion du fichier image : on utilise notre classe de service
            $this->uploaderHelper->uploadPostImage($post, $form->get('imageFile')->getData());

            // On persiste en BDD
            $this->manager->flush();

            // Message flash
            $this->addFlash('success', 'Article mis à jour.');

            // Redirection vers le dashboard admin
            return $this->redirectToRoute('admin.index');
        }

        return $this->render('admin/post/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/post/removeImage/{id<\d+>}", name="admin.post.removeImage")
     */
    public function removeImage(Post $post)
    {
        // Suppression du fichier image
        $this->uploaderHelper->removePostImageFile($post);

        // Vider le champ image de l'entité
        $post->setImage(null);

        // Mise à jour de l'entité en base de données
        $this->manager->flush();

        // Message flash
        $this->addFlash('success', 'Image supprimée.');

        // Redirection vers le dashboard admin
        return $this->redirectToRoute('admin.index');
    }

    /**
     * @Route("/admin/post/remove/{id<\d+>}", name="admin.post.remove")
     */
    public function remove(Post $post)
    {
        // Suppression du fichier image
        $this->uploaderHelper->removePostImageFile($post);

        // Suppression de l'entité
        $this->manager->remove($post);
        $this->manager->flush();

        // Message flash
        $this->addFlash('success', 'Article supprimé.');

        // Redirection vers le dashboard admin
        return $this->redirectToRoute('admin.index');
    }
}