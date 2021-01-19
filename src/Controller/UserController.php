<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Form\UserType;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\AuthenticatorInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class UserController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @Route("/signup", name="user.signup")
     */
    public function signup(Request $request,
                           UserPasswordEncoderInterface $encoder,
                           GuardAuthenticatorHandler $authenticatorHandler,
                           LoginFormAuthenticator $authenticator): Response
    {
        $form = $this->createForm(UserType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $form->getData();

            // On récupère le mot de passe en clair rentré par l'internaute dans le formulaire
            $plainPassword = $form->get('plainPassword')->getData();

            // Hashage du mot de passe
            $hashedPassword = $encoder->encodePassword($user, $plainPassword);

            // Affectation du mot de passe hashé au champ password de l'entité User
            $user->setPassword($hashedPassword);

            // Persistance en base de données
            $this->manager->persist($user);
            $this->manager->flush();

            // Message flash
            $this->addFlash('success', 'Votre compte est créé, vous pouvez vous connecter.');

            return $authenticatorHandler->authenticateUserAndHandleSuccess($user, $request, $authenticator, 'main');

            // Redirection vers la page de connexion
            // return $this->redirectToRoute('security.login');
        }

        return $this->render('user/signup.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/user/bookmark/add/{id<\d+>}", name="user.addBookmark")
     */
    public function addBookmark(Post $post, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $this->getUser();
        $user->addBookmark($post);
//        $post->addUser($user);
        $this->manager->flush();

        if ($request->isXmlHttpRequest()) {
            return $this->json('ok');
        }

        $this->addFlash('success', 'Favori ajouté.');
        return $this->redirectToRoute('post.index', ['slug' => $post->getSlug()]);
    }

    /**
     * @Route("/user/bookmark/remove/{id<\d+>}", name="user.removeBookmark")
     */
    public function removeBookmark(Post $post, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $this->getUser();
        $user->removeBookmark($post);

        $this->manager->flush();

        if ($request->isXmlHttpRequest()) {
            return $this->json('ok');
        }

        $this->addFlash('success', 'Favori supprimé.');
        return $this->redirectToRoute('post.index', ['slug' => $post->getSlug()]);
    }

    /**
     * @Route("/bookmarks", name="user.bookmarks")
     */
    public function showBookmarks()
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $this->getUser();
        $bookmarks = $user->getBookmarks();

        return $this->render('user/bookmarks.html.twig', [
            'bookmarks' => $bookmarks
        ]);
    }
}
