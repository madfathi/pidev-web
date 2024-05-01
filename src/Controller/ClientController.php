<?php

namespace App\Controller;
use App\Repository\ClientRepository;
use App\Entity\Client;
use App\Form\ClientType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Twilio\Rest\Client as TwilioClient;;

#[Route('/client')]
class ClientController extends AbstractController
{
    #[Route('/', name: 'app_client_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $clients = $entityManager
            ->getRepository(Client::class)
            ->findAll();
    
        return $this->render('client/index.html.twig', [
            'clients' => $clients,
        ]);
    }

    #[Route('/new', name: 'app_client_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $client = new Client();
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($client);
            $entityManager->flush();
           // $this->envoyerSms();

            return $this->redirectToRoute('app_client_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('client/new.html.twig', [
            'client' => $client,
            'form' => $form,
        ]);
    }
        // private function envoyerSms(): void
        // {
        //     // Remplacer ces valeurs par vos identifiants Twilio
        //     $sid = 'ACf89503a1392b4622617d3ad4e06ada2f';
        //     $token = '3de4d1d70e36ca995e200a83dc012362';
        //     $from = '+19033543619 ';
    
    
        //     // Initialisez le client Twilio
        //     $twilioClient = new TwilioClient($sid, $token);    
    
        //     // Remplacer votre_numero_destinataire par le numéro de téléphone du destinataire
        //     $numeroDestinataire = '+21625980858';
    
    
        //     // Message à envoyer
        //     $message = 'ajouter client';
    
    
        //     // Envoyer le SMS
        //     $twilioClient->messages->create(
        //         $numeroDestinataire,
        //         [
        //             'from' => $from,
        //             'body' => $message
        //         ]
        //     );
    
    
           
        // }
    

    #[Route('/{idC}', name: 'app_client_show', methods: ['GET'])]
    public function show(Client $client): Response
    {
        return $this->render('client/show.html.twig', [
            'client' => $client,
        ]);
    }

    #[Route('/{idC}/edit', name: 'app_client_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Client $client, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_client_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('client/edit.html.twig', [
            'client' => $client,
            'form' => $form,
        ]);
    }

    #[Route('/{idC}', name: 'app_client_delete', methods: ['POST'])]
    public function delete(Request $request, Client $client, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$client->getIdC(), $request->request->get('_token'))) {
            $entityManager->remove($client);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_client_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/newF', name: 'app_client_newF', methods: ['GET', 'POST'])]
    public function newF(Request $request, EntityManagerInterface $entityManager): Response
    {
        $client = new Client();
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // zid client.id= session id  bech ywali yasna3 client 3ando nafs el id que el user el connecté + test unicité ( user yasna3 client we7ed barka)
            $entityManager->persist($client);
            $entityManager->flush();

            return $this->redirectToRoute('app_client_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('client/newF.html.twig', [
            'client' => $client,
            'form' => $form,
        ]);
    }

   


#[Route('/pdf', name: 'PDF_Seance',methods: ['GET'])]
    public function pdf(ClientRepository $clientRepository)
    {
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
    
        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('client/PDF.html.twig', [
            'client' => $clientRepository->findAll(),
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
        $client= $entityManager->getRepository(Client::class)->find($id);

        if (!$client) {
            throw $this->createNotFoundException('Commande not found for ID ' . $id);
        }

        // Get the HTML content of the page you want to convert to PDF
        $html = $this->renderView('client/show_pdf.html.twig', [
            'client' => $client,
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
