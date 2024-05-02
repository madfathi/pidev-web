<?php

namespace App\Controller;

use App\Entity\Evenment;
use App\Form\EvenmentType;
use App\Entity\Client;
use App\Form\ReviewType;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;
use Symfony\Component\HttpFoundation\File\File;
use App\Repository\EvenmentRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;






class EvenmentController extends AbstractController
{

    #[Route('/display_evenment', name: 'display_evenment')]
    public function index(): Response
    {
        $evenment = $this->getDoctrine()->getManager()->getRepository(Evenment::class)->findAll();
        return $this->render('evenment/index.html.twig', [
            'e'=>$evenment
        ]);
    }

    #[Route('/Admin', name: 'display_admin')]
    public function indexAdmin(): Response
    {
       
        return $this->render('Admin/indexEvent.html.twig'
        );
    }
    
    #[Route('/front', name: 'display_front')]
    public function indexfront(EvenmentRepository $evenmentRepository): Response // Correct the argument type
    {
        $events = $evenmentRepository->findAllWithReviews();
      
    // Fetch top 3 events based on the total number of stars reviewed
    $top3Events = $evenmentRepository->findTopEvents(3);
     // Create a form instance for adding reviews
     $reviewForm = $this->createForm(ReviewType::class);

    return $this->render('front/index.html.twig', [
        'e' => $events,
        'top' => $top3Events, 
        'form' => $reviewForm->createView(), // Pass the form variable to the template

        ]);
    }
    
    

    #[Route('/ajouterEvenment', name: 'ajouterEvenment')]
    public function ajouterEvenment(Request $request,EvenmentRepository $evenmentRepository): Response
    {
    $evenment = new Evenment();
    $transport =Transport::fromDsn('smtp://eroreror2001@gmail.com:bdltxnxaucgxmzst@smtp.gmail.com:587');
    $mailer = new Mailer($transport);
  
  

    $form = $this->createForm(EvenmentType::class, $evenment);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Handle image upload
        $image = $form->get('image')->getData();

        // Check if a file has been uploaded
        if ($image) {
            // Get the original filename
            $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            // Generate a unique identifier to append to the filename
            $uniqueIdentifier = uniqid();
            // Ensure that the unique identifier doesn't contain characters that could cause issues in filenames
            $safeUniqueIdentifier = preg_replace('/[^a-z0-9]/', '', $uniqueIdentifier);
            // Combine the original filename, the unique identifier, and the file extension
            $safeFilename = $originalFilename . '-' . $safeUniqueIdentifier . '.' . $image->getClientOriginalExtension();
            // Move the file to the directory where images are stored
            $image->move(
                $this->getParameter('images_directory'),
                $safeFilename
            );
            // Set the image path in the Evenment entity
            $evenment->setImage($safeFilename);
        }

        // Check if there are already five events in the specific month
        $eventsInMonth = $evenmentRepository->findEventsByMonth($evenment->getDateEvent());

        // Check if there are already five events in the month
        if (count($eventsInMonth) >= 5) {
            $this->addFlash('danger', 'There are already five events in this month. Please pick another month for this event.');
            return $this->redirectToRoute('ajouterEvenment');
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($evenment);
        $em->flush();

        

        // Fetch all clients' emails
        $clients = $this->getDoctrine()->getRepository(Client::class)->findAll();
        foreach ($clients as $client) {


// Generate PDF
$dompdf = new Dompdf();
$htmlContent = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        h1 {
            font-size: 28px;
            color: #333;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        h2 {
            font-size: 24px;
            color: #555;
            margin-bottom: 15px;
        }
        p {
            font-size: 18px;
            color: #777;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <h1>' . $evenment->getNomEvent() . '</h1>
    <h2>' . $evenment->getNomStar() . '</h2>
    <p><strong>Lieu:</strong> ' . $evenment->getLieuEvent() . '</p>
    <p><strong>Date:</strong> ' . $evenment->getDateEvent()->format('Y-m-d') . '</p>
</body>
</html>';
 $dompdf->loadHtml($htmlContent);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$pdfContent = $dompdf->output();

// Save the PDF to a temporary file
$pdfFilePath = sys_get_temp_dir() . '/event_details.pdf';
file_put_contents($pdfFilePath, $pdfContent);

// Attach PDF from the temporary file path
$email = (new Email())
    ->from('eroreror2001@gmail.com')
    ->to($client->getPrenom()) // Assuming getPrenom() returns the email address
    ->subject('EVENTS POWERFIT')
    ->text('Your text here')
    ->html('<h1 style="color: #fff300;background-color: #0073ff;width: 500px; padding: 16px 0; text-align:center; border-radius: 50px;">Un Nouvel Evenement est ajouté !! stay tuned!! </h1>')
    ->attachFromPath($pdfFilePath, 'event_details.pdf', 'application/pdf');

$mailer->send($email);

// Delete the temporary PDF file
unlink($pdfFilePath);
        }

        return $this->redirectToRoute('display_evenment');
    }
    return $this->render('evenment/createEvenment.html.twig', ['f' => $form->createView()]);
}

    #[Route('/supp_evenment/{idEvent}', name: 'supp_evenment')]
    public function supp_evenment($idEvent, ManagerRegistry $doctrine): Response
    {
        $repo=$doctrine->getRepository(Evenment::class);
        $evenment= $repo->find($idEvent);

       $em=$doctrine->getManager();

       $em ->remove($evenment);
       $em ->flush();

       return $this->redirectToRoute('display_evenment');
    }


    #[Route('/modifEvenment/{idEvent}', name: 'modifEvenment')]
    public function modiffEvenment(Request $request, $idEvent): Response
    {
        $evenment = $this->getDoctrine()->getManager()->getRepository(Evenment::class)->find($idEvent);
    
        $form = $this->createForm(EvenmentType::class,$evenment);
    
        $form->handleRequest($request);
    
        if($form->isSubmitted() && $form->isValid()){
            // Handle image upload
            $image = $form->get('image')->getData();
    
            // Check if a file has been uploaded
            if ($image) {
                // Get the original filename
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                // Generate a unique identifier to append to the filename
                $uniqueIdentifier = uniqid();
                // Ensure that the unique identifier doesn't contain characters that could cause issues in filenames
                $safeUniqueIdentifier = preg_replace('/[^a-z0-9]/', '', $uniqueIdentifier);
                // Combine the original filename, the unique identifier, and the file extension
                $safeFilename = $originalFilename . '-' . $safeUniqueIdentifier . '.' . $image->getClientOriginalExtension();
                // Move the file to the directory where images are stored
                $image->move(
                    $this->getParameter('images_directory'),
                    $safeFilename
                );
                // Set the image path in the Evenment entity
                $evenment->setImage($safeFilename);
            }
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            
            return $this->redirectToRoute('display_evenment');
        }
        return $this->render('evenment/updateEvenment.html.twig',['f'=>$form->createView()]);
    }

    #[Route('/tri-asc', name: 'tri_asc')]
    public function triAsc(Request $request): Response
    {
        $evenments = $this->getDoctrine()
            ->getRepository(Evenment::class)
            ->findBy([], ['dateEvent' => 'ASC']);

        return $this->render('evenment/index.html.twig', [
            'e' => $evenments,
        ]);
    }

    #[Route('/tri-desc', name: 'tri_desc')]
    public function triDesc(Request $request): Response
    {
        $evenments = $this->getDoctrine()
            ->getRepository(Evenment::class)
            ->findBy([], ['dateEvent' => 'DESC']);

        return $this->render('evenment/index.html.twig', [
            'e' => $evenments,
        ]);
    }

    /**
     * @Route("/search_route", name="search_route")
     */
    public function search(Request $request): Response
    {
        $searchQuery = $request->query->get('search');

        if (!empty($searchQuery)) {
            $entityManager = $this->getDoctrine()->getManager();
            $evenments = $entityManager->getRepository(Evenment::class)->createQueryBuilder('e')
                ->where('e.nomEvent LIKE :searchQuery')
                ->setParameter('searchQuery', $searchQuery.'%')
                ->getQuery()
                ->getResult();
        } else {
            // If search query is empty, just fetch all events
            $evenments = $this->getDoctrine()->getRepository(Evenment::class)->findAll();
        }

        return $this->render('evenment/index.html.twig', [
            'e' => $evenments,
            'searchQuery' => $searchQuery,
        ]);
    }
   
    

}
