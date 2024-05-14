<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;  // Add this import statement
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\LivraisonRepository;
use App\Repository\CommandeRepository;

class StatistiqueController extends AbstractController
{
    #[Route('/statistiques', name: 'statistiques')]
    public function statistiques(LivraisonRepository $livraisonRepository,CommandeRepository $commandeRepository): Response
    {
        $livreursStats = $livraisonRepository->getLivreurStatistics();
        $commandesPrixSup1000 = $commandeRepository->countByPrixGreaterThan(1000);
        $commandesPrixInf1000 = $commandeRepository->countByPrixLessThanOrEqual(1000);

        return $this->render('commande/statistiques.html.twig', [
            'livreursStats' => $livreursStats,
            'commandesPrixSup1000' => $commandesPrixSup1000,
            'commandesPrixInf1000' => $commandesPrixInf1000,
        ]);
    }
    
  

}
