<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Psr\Log\LoggerInterface;

final class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact')]
    public function index(
        Request $request,
        MailerInterface $mailer,
        LoggerInterface $logger
    ): Response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Honeypot anti-spam
            if ($form->get('honeypot')->getData()) {
                $this->addFlash('danger', 'Spam détecté.');
                return $this->redirectToRoute('contact');
            }

            $data = $form->getData();

            try {
                $emailMessage = (new TemplatedEmail())
                    ->from('contact@forge-and-dev.fr')
                    ->to('contact@forge-and-dev.fr')  
                    ->replyTo($data['email'])
                    ->subject('[Formulaire de Contact Forge & Dev] ' . $data['subject'])
                    ->htmlTemplate('email/contact.html.twig')
                    ->context([
                        'name' => $data['name'],
                        'phone' => $data['phone'],
                        'user_email' => $data['email'],
                        'subject' => $data['subject'],
                        'project_type' => $data['project_type'],
                        'message' => $data['message'],
                    ]);

                $mailer->send($emailMessage);

                $this->addFlash('success', 'Votre message a bien été envoyé !');
                return $this->redirectToRoute('contact');

            } catch (\Exception $e) {
                $logger->error('Erreur envoi email Contact :' . $e->getMessage());
                $this->addFlash(
                    'danger',
                    'Une erreur est survenue lors de l’envoi du message. Veuillez réessayer plus tard.'
                );
            }
        }

        return $this->render('contact/index.html.twig', [
            'contactForm' => $form->createView(),
        ]);
    }
}