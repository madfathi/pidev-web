<?php

namespace App\Controller;

use App\Entity\Fidelite;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Form\UserType;
use App\Form\FideliteType;
use App\Repository\FideliteRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Dompdf\Dompdf;
use Dompdf\Options;


class FideliteController extends AbstractController
{
    #[Route('/fidelite', name: 'app_fidelite')]
    public function index(): Response
    {
        return $this->render('fidelite/index.html.twig', [
            'controller_name' => 'FideliteController',
        ]);
    }
    
    #[Route('/readf', name: 'app_fidelite_i', methods: ['GET'])]
    public function read(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $fidelites = $entityManager->getRepository(Fidelite::class)->findAll();
        
        return $this->render('Fidelite/read.html.twig', [
            'fidelites' => $fidelites,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_fidelite_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id, EntityManagerInterface $entityManager): Response
    {
        $fidelite = $entityManager->getRepository(Fidelite::class)->find($id);

        if (!$fidelite) {
            throw $this->createNotFoundException('Fidelite with id ' . $id . ' not found');
        }

        $form = $this->createForm(FideliteType::class, $fidelite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Fidelite updated successfully');

            return $this->redirectToRoute('app_fidelite_i');
        }

        return $this->render('Fidelite/edit.html.twig', [
            'fidelite' => $fidelite,
            'form' => $form->createView(),
        ]);
    }
    #[Route('/fidelite_add', name: 'app_fidelite_add')]
public function afficherFormulaire(Request $request,EntityManagerInterface $entityManager): Response
{
    $fidelite = new Fidelite();
    $form = $this->createForm(UserType::class, $fidelite);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
       

     
    
        $entityManager->persist($fidelite);
        $entityManager->flush();
            $this->addFlash('message','le code promo a bien ete ajouter ');
            return $this->redirectToRoute('app_fidelite_i', [], Response::HTTP_SEE_OTHER);
        }
    

    return $this->render('fidelite/form.html.twig', [
        'fidelite' => $fidelite,
        'form' => $form->createView(),
    ]);
}
#[Route('/backrechercheAjax', name: 'backrechercheAjax')]
public function searchAjax(Request $request,FideliteRepository $fideliteRepository): Response
{
    $query = $request->query->get('q');
    $donne = $fideliteRepository->findByFideliteByCodePromo($query); // Adjust this method according to your actual search logic in CommandeRepository

    return $this->render('fidelite/read.html.twig', [
        'donne' => $donne,
    ]);
}

    #[Route('/{id}/delete', name: 'app_fidelite_delete', methods: ['POST'])]
    public function delete(Request $request, int $id, EntityManagerInterface $entityManager): Response
    {
        $fidelite = $entityManager->getRepository(Fidelite::class)->find($id);

        if (!$fidelite) {
            throw $this->createNotFoundException('Fidelite with id ' . $id . ' not found');
        }

        $entityManager->remove($fidelite);
        $entityManager->flush();

        $this->addFlash('success', 'Fidelite deleted successfully');

        return $this->redirectToRoute('app_fidelite_i');
    }
    #[Route('/{id}/generate-pdf', name: 'contrat_generate_pdf')]
            public function generatePdf($id): Response
            {
                // Fetch the fidelite entity by its ID
                $entityManager = $this->getDoctrine()->getManager();
                $fidelite = $entityManager->getRepository(fidelite::class)->find($id);
        
                if (!$fidelite) {
                    throw $this->createNotFoundException('fidelite not found for ID ' . $id);
                }
        
                // Get the HTML content of the page you want to convert to PDF
                $html = $this->renderView('fidelite/show_pdf.html.twig', [
                    'fidelite' => $fidelite,
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
}



