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

            /**
             * 🚦 LIMITATION D’ENVOI (DÉSACTIVÉE POUR LE MOMENT)
             *
             * Pour activer :
             * 1) Décommenter le use RateLimiterFactory
             * 2) Décommenter l’argument du contrôleur
             * 3) Décommenter le bloc ci-dessous
             */
            
            /*$limiter = $contactFormLimiter->create($request->getClientIp());
            if (!$limiter->consume(1)->isAccepted()) {
                $this->addFlash(
                    'danger',
                    'Vous avez atteint la limite d’envoi de messages. Merci de réessayer plus tard.'
                );
                return $this->redirectToRoute('contact');
            }
            
            */

            $data = $form->getData();

            try {
                $emailMessage = (new Email())
                    ->from('no-reply@forge-dev.fr')
                    ->replyTo($data['email'])
                    ->to('contact@forge-dev.fr')
                    ->subject('[Contact Forge & Dev] ' . $data['subject'])
                    ->html(
                        '<h3>Nouveau message de contact</h3>' .
                        '<p><strong>Nom :</strong> ' . htmlspecialchars($data['name']) . '</p>' .
                        '<p><strong>Téléphone :</strong> ' . htmlspecialchars($data['phone']) . '</p>' .
                        '<p><strong>Email :</strong> ' . htmlspecialchars($data['email']) . '</p>' .
                        '<p><strong>Type de projet :</strong> ' . htmlspecialchars($data['project_type']) . '</p>' .
                        '<p><strong>Message :</strong><br>' .
                        nl2br(htmlspecialchars($data['message'])) . '</p>'
                    );

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
