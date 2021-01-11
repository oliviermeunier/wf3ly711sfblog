<?php

namespace App\Controller\Admin;

use App\Form\PostType;
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
     * @Route("/admin/post/new", name="admin.post.new")
     */
    public function new(Request $request, SluggerInterface $slugger, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(PostType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $post = $form->getData();

            // Gestion du slug
            $slug = $slugger->slug($post->getTitle());
            $post->setSlug($slug);

            // Gestion de l'utilisateur associé à l'article
            $post->setUser($this->getUser());

            // Gestion du fichier image

            /**
             * @var UploadedFile
             */
            $uploadedFile = $form->get('imageFile')->getData();

            // Si l'administrateur a rempli le champ image...
            if ($uploadedFile) {

                // Normalisation du nom du fichier image
                $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $slugger->slug($originalFilename) . '-' . uniqid() . '.' . $uploadedFile->guessExtension();

                $post->setImage($newFilename);

                // Copie du fichier temporaire vers le répertoire de destination
                $uploadedFile->move('upload/post/image', $newFilename);
            }

            // On persiste en BDD
            $manager->persist($post);
            $manager->flush();

            // Message flash
            $this->addFlash('success', 'Article ajouté.');

            // Redirection vers le dashboard admin
            return $this->redirectToRoute('admin.index');
        }

        return $this->render('admin/post/new.html.twig', [
            'form' => $form->createView()
        ]);
    }
}