<?php

namespace App\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Panier;
use App\Entity\Produits;
use App\Repository\PanierRepository;
use App\Repository\ProduitsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;


class PanierController extends AbstractController
{
   
    #[Route('/afficher', name: 'app_commande_afficher')]
    public function index2(): Response
    {
        $em=$this->getDoctrine()->getRepository(Panier::class);
        $donnees=$em->findAll();
        return $this->render('base2.html.twig', [
            'donnees' => $donnees,
        ]);
    }
    #[Route('/deletee/{idp}', name: 'deletee')]
    public function supprimerCategory($idp): Response
    {
        $donnees=$this->getDoctrine()->getRepository(Panier::class)->find($idp);
        $em=$this->getDoctrine()->getManager();
        $em->remove($donnees);
        $em->flush();
        return $this->redirectToRoute('app_commande_afficher');


       
    }
    #[Route('/statistique_quantite_produits', name: 'statistique_quantite_produits')]
    public function statistiqueQuantiteProduits(): JsonResponse
{
    // Récupérer tous les articles du panier
    $panierItems = $this->getDoctrine()->getRepository(Panier::class)->findAll();

    // Initialiser un tableau pour stocker la quantité totale de chaque produit
    $quantiteParProduit = [];

    // Parcourir les articles du panier pour accumuler les quantités par produit
    foreach ($panierItems as $item) {
        $produitId = $item->getProdId();
        $quantite = $item->getQuantite();

        // Vérifier si le produit ID existe dans le tableau $quantiteParProduit
        if (isset($quantiteParProduit[$produitId])) {
            // Si oui, ajouter la quantité actuelle à la quantité existante
            $quantiteParProduit[$produitId] += $quantite;
        } else {
            // Sinon, initialiser la quantité pour ce produit
            $quantiteParProduit[$produitId] = $quantite;
        }
    }

    // Préparer les données finales à afficher
    $data = [];
    foreach ($quantiteParProduit as $produitId => $quantiteTotale) {
        // Récupérer l'objet Produit à partir de son identifiant
        $produit = $this->getDoctrine()->getRepository(Produits::class)->find($produitId);

        if ($produit) {
            $nomProduit = $produit->getNom();

            // Ajouter le nom du produit et sa quantité totale au tableau de données
            $data[] = [
                'nomp' => $nomProduit, // Utilisation de 'nomp' pour correspondre à l'usage dans le template JavaScript
                'quantite' => $quantiteTotale,
            ];
        }
    }

    // Retourner les données au format JSON
    return new JsonResponse($data);
}

#[Route('/statistiques', name: 'statistiques')]
     
public function statistiques(PanierRepository $panierRepository): Response
{
    // Récupérer les données pour les statistiques
    $statistiques = $panierRepository->getStatistiquesByProduit();

    // Retourner les données en JSON
    return $this->json($statistiques);
}
   
}
