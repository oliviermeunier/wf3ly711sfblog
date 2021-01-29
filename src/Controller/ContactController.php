<?php

namespace App\Controller;

use App\Form\ContactFormType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="contact.index")
     * @return Response
     */
    public function index(Request $request, MailerInterface $mailer): Response
    {
        $form = $this->createForm(ContactFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();

            $email = (new TemplatedEmail())
                ->from(new Address($data['email'], $data['firstname'] . ' ' . $data['lastname']))
                ->to('admin@my-site.com')
                ->replyTo(new Address($data['email'], $data['firstname'] . ' ' . $data['lastname']))
                ->subject('New message from contact form')
                ->context([
                    'contact' => $data
                ])
                ->htmlTemplate('contact/mail.html.twig');

            $mailer->send($email);
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form->createView()
        ]);
    }





//        $email = (new Email())
//            ->from('hello@example.com')
//            ->to('oli.meunier@gmail.com')
//            //->cc('cc@example.com')
//            //->bcc('bcc@example.com')
//            //->replyTo('fabien@example.com')
//            //->priority(Email::PRIORITY_HIGH)
//            ->subject('Time for Symfony Mailer!')
//            ->text('Sending emails is fun again!')
//            ->html('<p>See Twig integration for better HTML integration!</p>');
//
//        $mailer->send($email);
//
//        return $this->render('contact/index.html.twig', [
//            'controller_name' => 'ContactController',
//        ]);
//    }
}
