<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
//use Symfony\Component\RateLimiter\RateLimiterFactory;

final class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact')]
    public function index(
        Request $request,
        MailerInterface $mailer
        // RateLimiterFactory $contactFormLimiter
    ): Response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // ✅ Honeypot anti-spam
            if ($form->get('honeypot')->getData()) {
                $this->addFlash('danger', 'Spam détecté.');
                return $this->redirectToRoute('contact');
            }

            $data = $form->getData();

            try {
                $emailMessage = (new Email())
                    ->from('contact@forge-and-dev.fr')
                    ->to('contact@forge-and-dev.fr')
                    ->subject('[Contact Forge & Dev] ' . $data['subject'])
                    ->htmlTemplate('email/contact.html.twig')
                    ->contexte([
                        'name' => $data['name'],
                        'phone' => $data['phone'],
                        'email' => $data['email'],
                        'project_type' => $data['project_type'],
                        'message' => $data['message'],
                    ]);

                $mailer->send($emailMessage);

                $this->addFlash('success', 'Votre message a bien été envoyé !');
                return $this->redirectToRoute('contact');

            } catch (\Exception $e) {
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
