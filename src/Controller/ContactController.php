<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

final class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact')]
    public function index(Request $request, MailerInterface $mailer): Response
    {
        // Traitement du formulaire
        if ($request->isMethod('POST')) {
            $name = $request->request->get('name');
            $phone = $request->request->get('phone');
            $email = $request->request->get('email');
            $subject = $request->request->get('subject');
            $projectType = $request->request->get('project_type');
            $message = $request->request->get('message');

            // Validation
            if (!$name || !$phone || !$email || !$subject || !$projectType || !$message) {
                $this->addFlash('error', 'Veuillez remplir tous les champs du formulaire.');
                return $this->redirectToRoute('contact');
            }

            try {
                $emailMessage = (new Email())
                    ->from($email)
                    ->to('contact@forge-dev.fr')
                    ->subject($subject)
                    ->text("Nom: $name\nTéléphone: $phone\nType de projet: $projectType\n\n$message");

                $mailer->send($emailMessage);
                $this->addFlash('success', 'Votre message a bien été envoyé !');
            } 
            
            catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de l\'envoi du message.');
                return $this->redirectToRoute('contact');
            }

            return $this->redirectToRoute('contact');
        }

        return $this->render('contact/index.html.twig', [
            'controller_name' => 'ContactController',
        ]);
    }
}
