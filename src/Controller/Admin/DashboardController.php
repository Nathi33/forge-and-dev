<?php

namespace App\Controller\Admin;

use App\Entity\Actualite;
use App\Entity\Realisation;
use App\Form\ChangePasswordType;
use App\Form\ChangeEmailType;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);

        $urlActualites = $adminUrlGenerator
            ->setController(ActualiteCrudController::class)
            ->setAction('index')
            ->generateUrl();

        $urlRealisations = $adminUrlGenerator
            ->setController(RealisationCrudController::class)
            ->setAction('index')
            ->generateUrl();

        return $this->render('admin/dashboard.html.twig', [
            'urlActualites' => $urlActualites,
            'urlRealisations' => $urlRealisations,
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Forge & Dev - Administration');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Tableau de bord', 'fa fa-home');

        yield MenuItem::section('Contenu');
        yield MenuItem::linkToCrud('Actualités', 'fa fa-newspaper', Actualite::class);
        yield MenuItem::linkToCrud('Réalisations', 'fa fa-images', Realisation::class);

        yield MenuItem::section('Compte');
        yield MenuItem::linkToRoute('Changer le mot de passe', 'fa fa-key', 'admin_change_password');
        yield MenuItem::linkToRoute('Changer l\'adresse email', 'fa fa-envelope', 'admin_change_email');
    }

    #[Route('/admin/change-password', name: 'admin_change_password')]
    public function changePassword(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();

            if (!$passwordHasher->isPasswordValid(
                $user,
                $form->get('currentPassword')->getData()
            )) {
                $this->addFlash('danger', 'Mot de passe actuel incorrect.');
            } else {
                $hashedPassword = $passwordHasher->hashPassword(
                    $user,
                    $form->get('newPassword')->getData()
                );
                $user->setPassword($hashedPassword);
                $entityManager->flush();

                $this->addFlash('success', 'Mot de passe modifié avec succès.');
                return $this->redirectToRoute('admin');
            }
        }

        return $this->render('admin/change_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/change-email', name: 'admin_change_email')]
    public function changeEmail(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer
    ): Response {
        $user = $this->getUser();
        $form = $this->createForm(ChangeEmailType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $currentPassword = $form->get('currentPassword')->getData();
            $newEmail = $form->get('newEmail')->getData();

            // Vérifie le mot de passe actuel
            if (!$passwordHasher->isPasswordValid($user, $currentPassword)) {
                $this->addFlash('danger', 'Mot de passe actuel incorrect.');
            } elseif ($user->getEmail() === $newEmail) {
                $this->addFlash('warning', 'La nouvelle adresse email est identique à l\'actuelle.');
            } else {
                // Stocker le nouvel email en attente
                $user->setNewEmail($newEmail);
                $user->setEmailConfirmed(false);
                // Générer un token pour la confirmation par email
                $token = bin2hex(random_bytes(32));
                $user->setResetToken($token);
                $user->setResetTokenExpiresAt(new \DateTime('+1 hour'));
                $entityManager->flush();

                // Générer le lien de confirmation
                $confirmUrl = $this->generateUrl('admin_confirm_email', ['token' => $token], true);

                // Envoyer le mail de confirmation
                $emailMessage = (new Email())
                    ->from('contact@forge-and-dev.fr')
                    ->to($newEmail)
                    ->subject('Confirmez votre nouvelle adresse email')
                    ->html(
                        <<<HTML
                        <div style="font-family: Arial, sans-serif; color: #333; line-height: 1.5;">
                            <h2 style="color: #c48a3a;">Confirmation de votre adresse email</h2>
                            <p>Bonjour,</p>
                            <p>Vous avez demandé à changer votre adresse email sur votre compte Forge & Dev.</p>
                            <p style="text-align: center; margin: 30px 0;">
                                <a href="{$confirmUrl}" 
                                style="background-color: #c48a3a; color: #fff; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold;">
                                Confirmer mon email
                                </a>
                            </p>
                            <p>Si vous n’avez pas demandé ce changement, vous pouvez ignorer cet email.</p>
                            <hr style="border: none; border-top: 1px solid #b5b5b5;">
                            <p style="font-size: 0.85rem; color: #7d7d7d;">
                                Forge & Dev – Administration
                            </p>
                        </div>
                        HTML
                    );
                $mailer->send($emailMessage);

                $this->addFlash('success', 'Un email de confirmation a été envoyé à votre nouvelle adresse.');
                return $this->redirectToRoute('admin');
            }
        }

        return $this->render('admin/change_email.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/admin/confirm-email/{token}', name: 'admin_confirm_email')]
    public function confirmEmail(
        string $token,
        EntityManagerInterface $entityManager
    ): Response {
        $user = $entityManager->getRepository(User::class)
            ->findOneBy(['resetToken' => $token]);

        if (!$user || $user->getResetTokenExpiresAt() < new \DateTime()) {
            $this->addFlash('danger', 'Lien de confirmation invalide ou expiré.');
            return $this->redirectToRoute('admin');
        }

        // Confirme le nouvel email
        $user->setEmail($user->getNewEmail());
        $user->setNewEmail(null);
        $user->setEmailConfirmed(true);
        $user->setResetToken(null);
        $user->setResetTokenExpiresAt(null);
        $entityManager->flush();

        $this->addFlash('success', 'Adresse email confirmée avec succès.');
        return $this->redirectToRoute('admin');
    }
}