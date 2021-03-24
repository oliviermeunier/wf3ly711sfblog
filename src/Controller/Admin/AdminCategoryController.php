<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Post;
use App\Form\PostType;
use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use App\Service\UploaderHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class AdminCategoryController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @Route("/admin/category/list", name="admin.category.index")
     */
    public function index(CategoryRepository $categoryRepository)
    {
        return $this->render('admin/category/index.html.twig', [
            'categories' => $categoryRepository->findBy([], ['name' => 'ASC'])
        ]);
    }

    /**
     * @Route("/admin/category/new", name="admin.category.new")
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
     * @Route("/admin/category/edit/{id<\d+>}", name="admin.category.edit")
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
     * @Route("/admin/category/remove/{id<\d+>}", name="admin.category.remove")
     */
    public function remove(Category $category)
    {
        // Suppression de l'entité
        $this->manager->remove($category);
        $this->manager->flush();

        // Message flash
        $this->addFlash('success', 'Catégorie supprimée.');

        // Redirection vers le dashboard admin
        return $this->redirectToRoute('admin.category.index');
    }
}