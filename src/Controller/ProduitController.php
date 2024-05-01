<?php

namespace App\Controller;

use App\Entity\Produits;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProduitController extends AbstractController
{
    #[Route('/produit', name: 'app_produit')]
    public function index(): Response
    {
        $em=$this->getDoctrine()->getRepository(Produits::class);
        $donnees=$em->findAll();
        return $this->render('pa.html.twig', [
            'donnees' => $donnees,
        ]);
    }
}
