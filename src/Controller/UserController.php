<?php

namespace App\Controller;

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
     * @Route("/signup", name="user.signup")
     */
    public function signup(Request $request,
                           UserPasswordEncoderInterface $encoder,
                           EntityManagerInterface $manager,
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
            $manager->persist($user);
            $manager->flush();

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
}
