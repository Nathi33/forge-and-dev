<?php

namespace App\Controller\Admin;

use App\Entity\Actualite;
use App\Entity\Realisation;
use App\Form\ChangePasswordType;
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
}