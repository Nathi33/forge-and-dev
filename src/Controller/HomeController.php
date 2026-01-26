<?php

namespace App\Controller;

use App\Repository\ActualiteRepository;
use App\Repository\RealisationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        ActualiteRepository $actualiteRepository,
        RealisationRepository $realisationRepository
    ): Response
    {
        $actualites = $actualiteRepository->findBy(
            ['actif' => true],
            ['ordre' => 'ASC', 'createdAt' => 'DESC'],
            5
        );

        $homeRealisations = $realisationRepository->findBy(
            ['homePage' => true],
            ['createdAt' => 'DESC'],
            3
        );

        return $this->render('home/index.html.twig', [
            'actualites' => $actualites,
            'homeRealisations' => $homeRealisations,
        ]);
    }
}
