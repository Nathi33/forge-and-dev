<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

final class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact')]
    public function index(Request $request, MailerInterface $mailer): Response
    {
        // Création du formulaire
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {

            // ✅ Vérification honeypot anti-spam
            if ($form->get('honeypot')->getData()) {
                $this->addFlash('danger', 'Spam détecté.');
                return $this->redirectToRoute('contact');
            }

            // Récupération des données du formulaire
            $data = $form->getData();

            try {
                // Création de l'email
                $emailMessage = (new Email())
                    ->from('no-reply@forge-dev.fr')      // From fixe pour éviter les rejets
                    ->replyTo($data['email'])            // Réponse vers l'utilisateur
                    ->to('contact@forge-dev.fr')         // Destinataire
                    ->subject("[Contact Forge & Dev] {$data['subject']}")
                    ->html(
                        "<h3>Nouveau message de contact</h3>" .
                        "<p><strong>Nom :</strong> " . htmlspecialchars($data['name']) . "</p>" .
                        "<p><strong>Téléphone :</strong> " . htmlspecialchars($data['phone']) . "</p>" .
                        "<p><strong>Email :</strong> " . htmlspecialchars($data['email']) . "</p>" .
                        "<p><strong>Type de projet :</strong> " . htmlspecialchars($data['project_type']) . "</p>" .
                        "<p><strong>Message :</strong><br/>" . nl2br(htmlspecialchars($data['message'])) . "</p>"
                    );

                // Envoi de l'email
                $mailer->send($emailMessage);

                $this->addFlash('success', 'Votre message a bien été envoyé !');
                return $this->redirectToRoute('contact');
            } catch (\Exception $e) {
                // Gestion des erreurs d'envoi
                $this->addFlash('danger', 'Une erreur est survenue lors de l\'envoi du message. Veuillez réessayer plus tard.');
            }
        }

        // Affichage du formulaire
        return $this->render('contact/index.html.twig', [
            'contactForm' => $form->createView(),
        ]);
    }
}
