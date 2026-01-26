<?php

namespace App\Controller;

use App\Repository\RealisationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RealisationController extends AbstractController
{
    #[Route('/realisation', name: 'realisation')]
    public function index(RealisationRepository $realisationRepository): Response
    {
        $realisations = $realisationRepository->findAllOrdered();
        
        return $this->render('realisation/index.html.twig', [
            'realisations' => $realisations,
        ]);
    }
}
