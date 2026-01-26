<?php

namespace App\Controller\Admin;

use App\Entity\Actualite;
use App\Entity\Realisation;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        // Récupérer le service via le container pour ne pas casser la signature
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
    }
}
