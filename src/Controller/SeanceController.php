<?php

namespace App\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Seance;
use App\Form\SeanceType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Doctrine\ORM\EntityRepository; 
use App\Repository\SeanceRepository;
use Dompdf\Dompdf;
use Dompdf\Options;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use App\Service\SmsGenerator;
class SeanceController extends AbstractController
{
    #[Route('/seance', name: 'app_seance')]
    public function index(): Response
    {
        return $this->render('seance/index.html.twig', [
            'controller_name' => 'SeanceController',
        ]);
    }
    #[Route('/reads', name: 'app_seance_i', methods: ['GET'])]
    public function read(Request $request): Response
    {
        $searchQuery = $request->query->get('search');
        $sortField = $request->query->get('sortField', 'typeSeance_asc');
    
        $repository = $this->getDoctrine()->getRepository(Seance::class);
    
        $queryBuilder = $repository->createQueryBuilder('s');
        if ($searchQuery) {
            $queryBuilder->andWhere('LOWER(s.typeSeance) LIKE LOWER(:search)')
                ->setParameter('search', '%' . strtolower($searchQuery) . '%');
        }
    
        // Sorting
        if ($sortField === 'typeSeance_asc') {
            $queryBuilder->orderBy('s.typeSeance', 'ASC');
        } elseif ($sortField === 'typeSeance_desc') {
            $queryBuilder->orderBy('s.typeSeance', 'DESC');
        }
    
        $seances = $queryBuilder->getQuery()->getResult();
    
        return $this->render('seance/read.html.twig', [
            'seances' => $seances,
            'searchQuery' => $searchQuery,
            'sortField' => $sortField, // Pass the sort field to the template
        ]);
    }
    
    public function search(Request $request): JsonResponse
    {
        $query = $request->query->get('q');
        $repository = $this->getDoctrine()->getRepository(Seance::class);
        $seances = $repository->createQueryBuilder('s')
            ->andWhere('s.typeSeance LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->getQuery()
            ->getResult();
    
        $results = [];
        foreach ($seances as $seance) {
            $results[] = [
                'typeSeance' => $seance->getTypeSeance(),
                // Add more fields if needed
            ];
        }
    
        return new JsonResponse($results);
    }
    #[Route('/edit', name: 'app_seance_edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager): Response
    {
        $id = $request->query->get('id');
        $seance = $entityManager->getRepository(Seance::class)->find($id);
        
        if (!$seance) {
            throw $this->createNotFoundException('Seance with id ' . $id . ' not found');
        }
    
        $form = $this->createForm(SeanceType::class, $seance);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('categorie')->getData();
    
            if($imageFile){
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename.'.'.$imageFile->guessExtension();
              
                $seance->setCategorie($newFilename);
                $entityManager->persist($seance);
                $entityManager->flush();
                $this->addFlash('message','le Restaurant a bien ete ajouter ');
                return $this->redirectToRoute('app_seance_i', [], Response::HTTP_SEE_OTHER);
        }
    }
    
        return $this->render('seance/edit.html.twig', [
            'seance' => $seance,
            'form' => $form->createView(),
        ]);
    }
      /**
     * @Route("/pdf", name="PDF_Seance", methods={"GET"})
     */
    #[Route('/pdf', name: 'PDF_Seance',methods: ['GET'])]
    public function pdf(SeanceRepository $seanceRepository)
    {
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
    
        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('seance/PDF.html.twig', [
            'seances' => $seanceRepository->findAll(),
        ]);
    
        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        // Setup the paper size and orientation
        $dompdf->setPaper('A3', 'portrait');
    
        // Render the HTML as PDF
        $dompdf->render();
    
        // Generate PDF file content
        $output = $dompdf->output();
    
        // Write file to the temporary directory
        $pdfFilepath = tempnam(sys_get_temp_dir(), 'pdf');
        file_put_contents($pdfFilepath, $output);
    
        // Return the PDF as a response
        return new BinaryFileResponse($pdfFilepath);
    }

    #[Route('/{id}/generate-pdf', name: 'contrat_generate_pdf')]
    public function generatePdf($id): Response
    {
        // Fetch the Commande entity by its ID
        $entityManager = $this->getDoctrine()->getManager();
        $seance= $entityManager->getRepository(Seance::class)->find($id);

        if (!$seance) {
            throw $this->createNotFoundException('Commande not found for ID ' . $id);
        }

        // Get the HTML content of the page you want to convert to PDF
        $html = $this->renderView('seance/show_pdf.html.twig', [
            'seance' => $seance,
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


    #[Route('/backrechercheAjaxx', name: 'backrechercheAjaxx')]
    public function searchAjax(Request $request, SeanceRepository $seanceRepository): Response
    {
        $query = $request->query->get('q');
        $seances = $seanceRepository->findBySeanceByNom($query); // Adjust this method according to your actual search logic in CommandeRepository

        return $this->render('seance/read.html.twig', [
            'seances' => $seances,
        ]);
    }
    #[Route('/calendar', name: 'calendrier')]
    public function calendar(Request $request, SeanceRepository $seanceRepository): Response
    {
       

        return $this->render('seance/calendar.html.twig');
    }
    #[Route('/statis', name: 'stati')]
    public function statistiques(){
      
        return $this->render('seance/statistique.html.twig');
    }

    public function statistics(): Response
    {
        $repository = $this->getDoctrine()->getRepository(Seance::class);
        $seances = $repository->findAll();
    
        // Initialiser un tableau pour stocker les statistiques
        $statistics = [];
    
        // Calculer le nombre de séances par type
        foreach ($seances as $seance) {
            $typeSeance = $seance->getTypeSeance();
            if (!isset($statistics[$typeSeance])) {
                $statistics[$typeSeance] = 0;
            }
            $statistics[$typeSeance]++;
        }
    
        // Renvoyer les statistiques au format JSON
        return $this->json($statistics);
    }

    #[Route('/fetch_seances', name: 'fetch_seances')]
    public function fetchSeances(): Response
    {
        $seances = $this->getDoctrine()->getRepository(Seance::class)->findAll();
    
        $seancesData = [];
        foreach ($seances as $seance) {
            $seancesData[] = [
                'typeSeance' => $seance->getTypeSeance(),
                'categorie' => $seance->getCategorie(),
                'nb_Maximal' => $seance->getNbMaximal(),
                'dateFin' => $seance->getDateFin()->format('Y-m-d'),
            
            ];
        }
    
        return $this->json($seancesData);
    }



    #[Route('/seance_add', name: 'app_seance_add')]
    public function afficherFormulaire(Request $request,EntityManagerInterface $entityManager): Response
    {
        $seance = new Seance();
        $form = $this->createForm(SeanceType::class, $seance);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('categorie')->getData();
    
            if($imageFile){
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename.'.'.$imageFile->guessExtension();
              
                $seance->setCategorie($newFilename);
                $entityManager->persist($seance);
                $entityManager->flush();
                $this->addFlash('message','le Restaurant a bien ete ajouter ');
                return $this->redirectToRoute('app_seance_i', [], Response::HTTP_SEE_OTHER);
            }else{
            $entityManager->persist($seance);
            $entityManager->flush();
                $this->addFlash('message','le Voyage a bien ete ajouter ');
                return $this->redirectToRoute('app_seance_i', [], Response::HTTP_SEE_OTHER);
            }
        }
       
      

        return $this->render('seance/form.html.twig', [
            'seance' => $seance,
            'form' => $form->createView(),
        ]);
    }
    #[Route('/{id}/delets', name: 'app_seance_delete', methods: ['GET', 'POST'])]
    public function delete(Request $request, int $id, EntityManagerInterface $entityManager): Response
    {
        $seance = $entityManager->getRepository(Seance::class)->find($id);
    
        if (!$seance) {
            throw $this->createNotFoundException('Seance with id ' . $id . ' not found');
        }
    
        $entityManager->remove($seance);
        $entityManager->flush();
    
        return $this->redirectToRoute('app_seance_i', [], Response::HTTP_SEE_OTHER);
    }


   

}
