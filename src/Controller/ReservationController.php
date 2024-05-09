<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Seance;
use App\Form\SeanceType;
use App\Entity\Reservation;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Form\ReservationType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Mailer\MailerInterface;
use App\Service\SmsGenerator;
use Knp\Component\Pager\PaginatorInterface;
use App\Entity\User;
class ReservationController extends AbstractController
{
    #[Route('/reservation', name: 'app_reservation')]
    public function index(): Response
    {
        return $this->render('reservation/index.html.twig', [
            'controller_name' => 'ReservationController',
        ]);
    }



    
    #[Route('/main', name: 'app_main')]
    public function index1(): Response
    {
        return $this->render('front55.html.twig');
    }
    #[Route('/readr', name: 'app_reservation_i', methods: ['GET'])]
    public function read(): Response
    {
    
        $repository= $this->getDoctrine()->getRepository(Reservation::class)->findAll();
     
        return $this->render('Reservation/read.html.twig',['reservations'=>$repository,
    ]);
}
#[Route('/reserva', name: 'app_reserv_i', methods: ['GET'])]
public function reserver(Request $request, PaginatorInterface $paginator): Response
{
    $query = $this->getDoctrine()->getRepository(Seance::class)->createQueryBuilder('s')->getQuery();

    $pagination = $paginator->paginate(
        $query,
        $request->query->getInt('page', 1), // Get the current page or default to 1
        10 // Number of items per page
    ); 
    $this->addFlash('success', 'Votre réservation a été effectuée avec succès.');

    return $this->render('Reservation/form.html.twig', ['seances' => $pagination]);
}
#[Route('/makereserv', name: 'reservation_new', methods: ['GET', 'POST'])]
public function new(Request $request,MailerController $mailer, MailerInterface $test,PaginatorInterface $paginator): Response
{
    $result = null;
   
   
    $idSeance = $request->query->get('idSeance');

    if (!$idSeance) {
        // Handle the case where idSeance is not provided
        // For example, you can redirect or display an error message
      
    }

    $entityManager = $this->getDoctrine()->getManager();

    // Load the corresponding Seance entity from the database based on the idSeance parameter
    $seance = $entityManager->getRepository(Seance::class)->find($idSeance);

    if (!$seance) {
        $this->addFlash('error', 'ID de séance manquant.');
        return $this->redirectToRoute('app_reserv_i'); // Redirect to a specific route

       
    }
    if ($seance->getNbMaximal() <= 0) {
        $this->addFlash('error', 'Plus de places disponibles pour cette séance.');
        return $this->redirectToRoute('app_reserv_i'); // Redirect to a specific route
    }

    // Decrement the nbMaximal attribute of the Seance entity
    $seance->setNbMaximal($seance->getNbMaximal() - 1);

    // Create a new instance of Reservation and assign the Seance to it
    $reservation = new Reservation();
    $reservation->setIdSeance($seance); // Make sure you have a setIdSeance method in your Reservation entity

    // Create the form based on the reservation with the assigned Seance
    $form = $this->createForm(ReservationType::class, $reservation);

    $form->handleRequest($request);

    $user_id = $this->get('session')->get('user_id');
    $user = $entityManager->getRepository(User::class)->find($user_id);

 $reservatio = new Reservation();
    $reservatio->setUsername($user->getNom());
    $reservatio->setEmail($user->getAddEmail());
    $reservatio->setTypeReservation(  $seance->getTypeSeance());
    $numTlfn = intval($user->getNumTlfn());
    $reservatio->setPhone($numTlfn);  
    $reservatio->setIdSeance($seance);  
 
  
    $entityManager->persist( $reservatio);
    $entityManager->flush();

       // $email = $form->get('fathimaddeh88@gmail.com')->getData();
       $result = $mailer->sendEmail($test);
      
    
    $smsGenerator= new SmsGenerator();
   
    $number_test=$_ENV['twilio_to_number'];
    $smsGenerator->sendSms($number_test , 'admin', 'votre reservation success');
  
      
    $query = $this->getDoctrine()->getRepository(Seance::class)->createQueryBuilder('s')->getQuery();

    $pagination = $paginator->paginate(
        $query,
        $request->query->getInt('page', 1), // Get the current page or default to 1
        10 // Number of items per page
    ); 
    $this->addFlash('success', 'Votre réservation a été effectuée avec succès.');

   
    return $this->render('Reservation/form.html.twig', ['seances' => $pagination]);
}
#[Route('/test-email', name: 'test_email')]
public function testEmail(MailerController $mailer, MailerInterface $test): Response
{
    $result = $mailer->sendEmail($test);
    return new Response($result);
}
#[Route('/makereservt', name: 'reservation_neww', methods: ['GET', 'POST'])]
public function new3(Request $request,MailerController $mailer, MailerInterface $test,PaginatorInterface $paginator): Response
{
    $idSeance = $request->query->get('idSeance');

    if (!$idSeance) {
        // Handle the case where idSeance is not provided
        // For example, you can redirect or display an error message
      
    }

    $entityManager = $this->getDoctrine()->getManager();
   

    // Load the corresponding Seance entity from the database based on the idSeance parameter
    $seance = $entityManager->getRepository(Seance::class)->find($idSeance);

    if (!$seance) {
        $this->addFlash('error', 'ID de séance manquant.');
        return $this->redirectToRoute('app_reserv_i'); // Redirect to a specific route

       
    }
   

    // Create a new instance of Reservation and assign the Seance to it
    $user_id = $this->get('session')->get('user_id');
    $user = $entityManager->getRepository(User::class)->find($user_id);

 $reservatio = new Reservation();
    $reservatio->setUsername($user->getNom());
    $reservatio->setEmail($user->getAddEmail());
    $reservatio->setTypeReservation(  $seance->getTypeSeance());
    $numTlfn = intval($user->getNumTlfn());
    $reservatio->setPhone($numTlfn);  
    $reservatio->setIdSeance($seance);  
 
  
    $entityManager->persist( $reservatio);
    $entityManager->flush();

       // $email = $form->get('fathimaddeh88@gmail.com')->getData();
       $result = $mailer->sendEmail($test);
      
    
    $smsGenerator= new SmsGenerator();
   
    $number_test=$_ENV['twilio_to_number'];
    $smsGenerator->sendSms($number_test , 'admin', 'votre reservation success');
  
      
    $query = $this->getDoctrine()->getRepository(Seance::class)->createQueryBuilder('s')->getQuery();

    $pagination = $paginator->paginate(
        $query,
        $request->query->getInt('page', 1), // Get the current page or default to 1
        10 // Number of items per page
    ); 

    return $this->render('Reservation/form.html.twig', ['seances' => $pagination]);

   
}
#[Route('/makereserva', name: 'reservation_new2', methods: ['GET', 'POST'])]
public function new2(Request $request,PaginatorInterface $paginator): Response
{
    $idSeance = $request->query->get('idSeance');

    if (!$idSeance) {
        // Handle the case where idSeance is not provided
        // For example, you can redirect or display an error message
      
    }

    $entityManager = $this->getDoctrine()->getManager();

    // Load the corresponding Seance entity from the database based on the idSeance parameter
    $seance = $entityManager->getRepository(Seance::class)->find($idSeance);

    if (!$seance) {
        $this->addFlash('error', 'ID de séance manquant.');
        return $this->redirectToRoute('app_reserv_i'); // Redirect to a specific route

       
    }

    // Create a new instance of Reservation and assign the Seance to it
   // Make sure you have a setIdSeance method in your Reservation entity

    // Create the form based on the reservation with the assigned Seance
  

    return $this->render('Reservation/makereserva.html.twig', [
        
        'seance' => $seance, // Pass the idSeance variable to the template
    ]);
}


#[Route('/{id}/deletr', name: 'app_reservation_delete', methods: ['GET', 'POST'])]
public function delete(Request $request, int $id, EntityManagerInterface $entityManager): Response
{
    $reservation = $entityManager->getRepository(Reservation::class)->find($id);

if (!$reservation) {
    throw $this->createNotFoundException('Reservation with id ' . $id . ' not found');
}

$idSeance = $reservation->getIdSeance();

$seance = $entityManager->getRepository(Seance::class)->find($idSeance);

if (!$seance) {
    throw $this->createNotFoundException('Seance with id ' . $idSeance . ' not found');
}

// Increment nbMaximal
$seance->setNbMaximal($seance->getNbMaximal() + 1);

$entityManager->remove($reservation);
$entityManager->flush();

return $this->redirectToRoute('app_reservation_i', [], Response::HTTP_SEE_OTHER);
}
#[Route('/editr', name: 'app_reservation_edit')]
public function edit(Request $request, EntityManagerInterface $entityManager): Response
{
    $id = $request->query->get('id');
    $Reservation = $entityManager->getRepository(Reservation::class)->find($id);
    
    if (!$Reservation) {
        throw $this->createNotFoundException('Seance with id ' . $id . ' not found');
    }

    $form = $this->createForm(ReservationType::class, $Reservation);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->flush();
        $this->addFlash('message', 'La Seance a bien ete modifier');
        return $this->redirectToRoute('app_reservation_i', [], Response::HTTP_SEE_OTHER);
    }

    return $this->render('Reservation/edit.html.twig', [
        'reservation' => $Reservation,
        'form' => $form->createView(),
    ]);
}




}