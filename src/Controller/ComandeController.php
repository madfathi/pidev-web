<?php

namespace App\Controller;
use App\Entity\Commande;
use App\Form\ModifiercommandeType;
use App\Repository\ComandeRepository;
use App\Repository\CommandeRepository;
use CommandeRepository as GlobalCommandeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Dompdf\Dompdf;
use Dompdf\Options;

class ComandeController extends AbstractController
{
    #[Route('/comande', name: 'app_comande')]
    public function index(): Response
    {
        $em=$this->getDoctrine()->getRepository(Commande::class);
        $donne=$em->findAll();
        return $this->render('base.html.twig', [
            'donne' => $donne,
        ]);
    }
    #[Route('/delete/{idc}', name: 'delete')]
    public function supprimerCategory($idc): Response
    {
        $donne=$this->getDoctrine()->getRepository(Commande::class)->find($idc);
        $em=$this->getDoctrine()->getManager();
        $em->remove($donne);
        $em->flush();
        return $this->redirectToRoute('app_comande');


       
    }
    #[Route('/modifier/{idc}', name: 'modifier')]
    public function modifierCommande(Request $request, $idc): Response
    {
        $prod = $this->getDoctrine()->getManager()->getRepository(Commande::class)->find($idc);


        $form = $this->createForm(ModifiercommandeType::class, $prod);


        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($prod);//ajout
            $em->flush();// commit
            $this->addFlash(
                'notice', 'commande a été bien modifiée ! '
            );
            return $this->redirectToRoute('app_comande');

        }

        return $this->render('comande/update.html.twig',
            ['form' => $form->createView()]
        );
    }
    
    #[Route('/backrechercheAjax', name: 'backrechercheAjax')]
    public function searchAjax(Request $request, ComandeRepository $commandeRepository): Response
    {
        $query = $request->query->get('q');
        $donne = $commandeRepository->findByCommandeByNom($query); // Adjust this method according to your actual search logic in CommandeRepository

        return $this->render('base.html.twig', [
            'donne' => $donne,
        ]);
    }
    #[Route('/TriPAB', name: 'app_tri_nom')]
            public function Tri( ComandeRepository $commandeRepository)
            {
                $donne =  $commandeRepository->orderByNomASC();
                return $this->render("base.html.twig", array( 'donne' => $donne));
            }
            #[Route('/{idc}/generate-pdf', name: 'contrat_generate_pdf')]
            public function generatePdf($idc): Response
            {
                // Fetch the Commande entity by its ID
                $entityManager = $this->getDoctrine()->getManager();
                $commande = $entityManager->getRepository(Commande::class)->find($idc);
        
                if (!$commande) {
                    throw $this->createNotFoundException('Commande not found for ID ' . $idc);
                }
        
                // Get the HTML content of the page you want to convert to PDF
                $html = $this->renderView('comande/show_pdf.html.twig', [
                    'commande' => $commande,
                ]);
        
                // Configure Dompdf options
                $options = new Options();
                $options->set('isHtml5ParserEnabled', true);
        
                // Instantiate Dompdf with the configured options
                $dompdf = new Dompdf($options);
        
                // Load HTML content into Dompdf
                $dompdf->loadHtml($html);
        
                // Set paper size and orientation
                $dompdf->setPaper('A4', 'portrait');
        
                // Render the HTML as PDF
                $dompdf->render();

    // Set response headers for PDF download
    $response = new Response($dompdf->output());
    $response->headers->set('Content-Type', 'application/pdf');
    $response->headers->set('Content-Disposition', 'attachment; filename="a.pdf"');

    return $response;
}
#[Route('/statistique_par_adresse', name: 'statistique_par_adresse')]
public function statistiqueParAdresse(): JsonResponse
{
    // Récupérer toutes les commandes
    $commandes = $this->getDoctrine()->getRepository(Commande::class)->findAll();

    // Initialiser un tableau pour stocker les adresses et leurs occurrences
    $adressesOccurrences = [];

    // Parcourir les commandes pour compter les occurrences de chaque adresse
    foreach ($commandes as $commande) {
        $adresse = $commande->getAddr();

        // Vérifier si l'adresse existe déjà dans le tableau des adresses
        if (isset($adressesOccurrences[$adresse])) {
            // Si l'adresse existe, incrémenter son nombre d'occurrences
            $adressesOccurrences[$adresse]++;
        } else {
            // Sinon, initialiser le nombre d'occurrences pour cette adresse à 1
            $adressesOccurrences[$adresse] = 1;
        }
    }
    $data = [];
    foreach ($adressesOccurrences as $adresse => $occurrences) {
        // Récupérer l'objet Produit à partir de son identifiant
        $produit = $this->getDoctrine()->getRepository(Commande::class)->findAll();

        if ($produit) {
           

            // Ajouter le nom du produit et sa quantité totale au tableau de données
            $data[] = [
                'adresse' => $adresse,
            'occurrences' => $occurrences,
            ];
        }
    }
    // Préparer les données finales au format requis pour le graphique
    $data = [];
    foreach ($adressesOccurrences as $adresse => $occurrences) {
        // Ajouter chaque adresse avec son nombre d'occurrences au tableau de données
        $data[] = [
            'adresse' => $adresse,
            'occurrences' => $occurrences,
        ];
    }

    // Retourner les données au format JSON
    return new JsonResponse($data);
}
    
}

