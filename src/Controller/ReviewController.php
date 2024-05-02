<?php

namespace App\Controller;

use App\Entity\Review;
use App\Entity\Evenment;
use App\Form\ReviewType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\EvenmentRepository;
use Symfony\Component\Routing\Annotation\Route;

class ReviewController extends AbstractController
{
    #[Route('/review', name: 'app_review')]
    public function index(): Response
    {
        $review = $this->getDoctrine()->getManager()->getRepository(Review::class)->findAll();
        return $this->render('review/index.html.twig', [
            'r'=>$review
        ]);
    }

    
  
    #[Route('/ajouter_review/{idEvent}', name: 'add_review')]

    public function addReview(Request $request, $idEvent, ManagerRegistry $doctrine): Response

    {
        $review = new Review();
        $form = $this->createForm(ReviewType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Find the event by id
            $event = $doctrine->getRepository(Evenment::class)->find($idEvent);

            // Set the event for the review
            $review->setIdEvent($event);

            // Save the review to the database
            $entityManager = $doctrine->getManager();
            $entityManager->persist($review);
            $entityManager->flush();

            return $this->redirectToRoute('display_front');
        }

        return $this->render('front/comment.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/supp_review/{idReview}', name: 'supp_review')]
    public function supp_review($idReview, ManagerRegistry $doctrine): Response
    {
        $repo=$doctrine->getRepository(Review::class);
        $review= $repo->find($idReview);

       $rw=$doctrine->getManager();

       $rw ->remove($review);
       $rw ->flush();

       return $this->redirectToRoute('app_review');
    }
}
