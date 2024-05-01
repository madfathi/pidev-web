<?php

namespace App\Controller;
use App\Repository\ClientRepository;
use App\Entity\Program;
use App\Form\ProgramType;
use App\Repository\ProgramRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Knp\Component\Pager\PaginatorInterface; 
use App\Service\StripeService;
use Stripe\Charge;
use Stripe\Stripe;


use App\Entity\Client;
use Symfony\Component\Mailer\MailerInterface;

#[Route('/program')]
class ProgramController extends AbstractController
{

    #[Route('/payment', name: 'app_payment1')]
    public function indexp(): Response
    {
        return $this->render('program/payment.html.twig', [
            'controller_name' => 'PaymentController',
            'stripe_key' => $_ENV["STRIPE_PUBLIC_KEY"],
        ]);
    }
    ///payment
    
    #[Route('/payment/create-charge', name: 'app_stripe_charge2', methods: ['POST'])]
    public function createCharge(Request $request)
    {
        Stripe::setApiKey($_ENV["STRIPE_SECRET_KEY"]);
        Charge::create ([
                "amount" => 5 * 100,
                "currency" => "usd",
                "source" => $request->request->get('stripeToken'),
                "description" => "Binaryboxtuts Payment Test"
        ]);
        $this->addFlash(
            'success',
            'Payment Successful!'
        );
        return $this->redirectToRoute('app_program_indexf', [], Response::HTTP_SEE_OTHER);
    }  

    
    #[Route('/', name: 'app_program_index', methods: ['GET'])]
    public function index(ProgramRepository $programRepository, Request $request): Response
    {
        $searchTerm = $request->query->get('search');
    
        if ($searchTerm) {
            $programs = $programRepository->findByTitre($searchTerm);
        } else {
            $programs = $programRepository->findAll();
        }
       
    
        return $this->render('program/index.html.twig', [
            'programs' => $programs,
            'search' => $searchTerm,

        ]);
    }
    #[Route('/c', name: 'app_program_indexC', methods: ['GET'])]
    public function indexc(ProgramRepository $programRepository, Request $request): Response
    {
        $searchTerm = $request->query->get('search');
    
        if ($searchTerm) {
            $programs = $programRepository->findByTitre($searchTerm);
        } else {
            $programs = $programRepository->findAll();
        }
    
        return $this->render('program/indexC.html.twig', [
            'programs' => $programs,
            'search' => $searchTerm,
        ]);
    }
    #[Route('/f', name: 'app_program_indexf', methods: ['GET'])]
    public function indexF(ProgramRepository $programRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $searchTerm = $request->query->get('search');
    
        if ($searchTerm) {
            $programs = $programRepository->findByTitre($searchTerm);
        } else {
            $programs = $programRepository->findAll();
        }
    
        // Paginate the results
        $pagination = $paginator->paginate(
            $programs, // The query to paginate
            $request->query->getInt('page', 1), // Get the page number from the request, default to 1
            3 // Number of items per page
        );
    
        return $this->render('program/indexf.html.twig', [
            'programs' => $programs,
            'search' => $searchTerm,
            'pagination' => $pagination,
            // 3adi el session
        ]);
    }
    

    #[Route('/new', name: 'app_program_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $program = new Program();
        $form = $this->createForm(ProgramType::class, $program);
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
                // Combine  the original filename, the unique identifier, and the file extension
                $safeFilename = $originalFilename . '-' . $safeUniqueIdentifier . '.' . $image->getClientOriginalExtension();
                // Move the file to the directory where images are stored
                $image->move(
                    $this->getParameter('images_directory'),
                    $safeFilename
                );
                // Set the image path in the Evenment entity
                $program->setImage($safeFilename);
            } 
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($program);
            $em->flush();

            return $this->redirectToRoute('app_program_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('program/new.html.twig', [
            'program' => $program,
            'form' => $form,
        ]);
       
    }
    

    #[Route('/{idP}', name: 'app_program_show', methods: ['GET'])]
    public function show(Program $program): Response
    {
        return $this->render('program/show.html.twig', [
            'program' => $program,
        ]);
    }

    #[Route('/{idP}/edit', name: 'app_program_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Program $program, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_program_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('program/edit.html.twig', [
            'program' => $program,
            'form' => $form,
        ]);
    }

    #[Route('/{idP}', name: 'app_program_delete', methods: ['POST'])]
    public function delete(Request $request, Program $program, EntityManagerInterface $entityManager): Response
    {
        if ($request->request->get('_token')) {
            $entityManager->remove($program);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_program_index', [], Response::HTTP_SEE_OTHER);
    }


   

  
#[Route('/tri-asc', name: 'tri_asc')]
public function triAsc(Request $request): Response
{
    $programs = $this->getDoctrine()
        ->getRepository(Program::class)
        ->findBy([], ['Prix' => 'ASC']);

    return $this->render('program/index.html.twig', [
        'programs' => $programs,
    ]);
}


#[Route('/tri-desc', name: 'tri_desc')]
public function triDesc(Request $request): Response
{
    $programs = $this->getDoctrine()
        ->getRepository(Program::class)
        ->findBy([], ['Prix' => 'DESC']);

    return $this->render('program/index.html.twig', [
        'programs' => $programs,
    ]);
}




#[Route('/{id}/choisir', name: 'app_program_choisir', methods: ['GET'])]
public function choisir(Request $request, $id, EntityManagerInterface $entityManager)
{
    $program = $this->getDoctrine()->getRepository(Program::class)->find($id);

    if (!$program) {
        return new Response('Program not found', Response::HTTP_NOT_FOUND);
    }
// 99 7ot fi blasetha id te3 session user hedhi mte3 el choisir bech ywali el id_c f wost programme ye5o valeur mte3 el user id 

    $clientId = 99; 
    $client = $this->getDoctrine()->getRepository(Client::class)->find($clientId);

    if (!$client) {
        return new Response('Client not found', Response::HTTP_NOT_FOUND);
    }

    $program->setEtat(1); 
    $program->setIdClient($client); // Set idClient to the retrieved Client entity
    $entityManager->persist($program);
    $entityManager->flush();

    $this->addFlash('success', 'Program pending');

    return $this->redirectToRoute('app_program_indexf'); 
}

#[Route('/{idP}/accept', name: 'app_program_accept', methods: ['GET'])]
public function accept(Request $request, $idP, EntityManagerInterface $entityManager)
{
    $program = $this->getDoctrine()->getRepository(Program::class)->find($idP);

    if (!$program) {
        return new Response('Program not found', Response::HTTP_NOT_FOUND);
    }

   

   
    $program->setEtat(2); 
    $entityManager->persist($program);
    $entityManager->flush();

    $this->addFlash('success', 'Program accepted successfully!');
    $templatePath = $this->getParameter('kernel.project_dir') . '/templates/program/mail.html.twig';
        $message = file_get_contents($templatePath);
        $message=str_replace("{{Titre}}",$program->getTitre(),$message);
        $message=str_replace("{{Niveau}}",$program->getNiveau(),$message);
        $message=str_replace("{{Description}}",$program->getDescription(),$message);
        $message=str_replace("{{Prix}}",$program->getPrix(),$message);



        $success=$this->sendMail($message,'Program confirmation');

    return $this->redirectToRoute('app_program_index'); 
}

private function sendMail($message,$subject):bool
    {
        require_once __DIR__ . '/../../public/mail.php';
        $mail->setFrom('salim.mahdi@esprit.tn', 'powerfit@noReplay');
        $mail->addAddress("salimmahdi680@gmail.com");
        $mail->Subject = $subject;
        $mail->Body    = $message;
        
            return $mail->send();
       
    }

    #[Route('/{id}/abon', name: 'app_program_abon', methods: ['GET'])]
    public function yourAction(string $id,ProgramRepository $programRepository): Response
    {
        $programs = $programRepository->findProgramsByClientId($id);

       
        return $this->render('program/index_abon.html.twig', [
            'programs' => $programs,
        ]);
    }




}