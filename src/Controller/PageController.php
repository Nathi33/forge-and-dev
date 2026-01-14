<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PageController extends AbstractController
{
    #[Route('/a_propos', name: 'a_propos')]
    public function index(): Response
    {
        return $this->render('page/a_propos.html.twig');
    }

    #[Route('/tarifs', name: 'tarifs')]
    public function tarifs(): Response
    {
        return $this->render('page/tarifs.html.twig');
    }

    #[Route('/mentions_legales', name: 'mentions_legales')]
    public function mentionsLegales(): Response
    {
        return $this->render('page/mentions_legales.html.twig');
    }

    #[Route('/politique_confidentialite', name: 'politique_confidentialite')]
    public function politiqueConfidentialite(): Response
    {
        return $this->render('page/politique_confidentialite.html.twig');   
    }
}
