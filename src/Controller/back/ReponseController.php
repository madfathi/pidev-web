<?php

namespace App\Controller;

use App\Entity\Reponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Form\ReponseFormType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ReponseRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Symfony\Contracts\Service\ServiceSubscriberTrait;
use Psr\Log\LoggerInterface;

class ReponseController extends AbstractController 
{
    #[Route('/reponse', name: 'reponse')]
    public function index(ReponseRepository $reponseRepository): Response
    {
        return $this->render('reponse/reponse_index.html.twig', [
            'reponses' => $reponseRepository->findAll(),
        ]);
    }
    

     #[Route('/reponse_new', name: 'reponse_new')]
    public function new(Request $request): Response
    {
        $reponse = new Reponse();

        $form = $this->createForm(ReponseFormType::class, $reponse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $reponse->setCreatedAt(new \DateTime('now'));
           
            $entityManager->persist($reponse);
            $entityManager->flush();

            return $this->redirectToRoute('reponse');
        }

        return $this->renderForm('reponse/new.html.twig',['form'=>$form]);
    }



    #[Route('/reponse_edit/{id}', name: 'reponse_edit')]
    public function edit(Request $request, int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $reponse = $entityManager->getRepository(Reponse::class)->find($id);
        $form = $this->createForm(ReponseFormType::class, $reponse);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
           
            $reponse->setCreatedAt(new \DateTime('now'));
        
            // save the changes to the database
            $entityManager->persist($reponse);
            $entityManager->flush();
        
            return $this->redirectToRoute('reponse');
        }
        
        return $this->renderForm('reponse/new.html.twig', ['form' => $form]);
        
        
    }

    #[Route('/reponse_delete/{id}', name: 'reponse_delete')]
    public function delete(reponse $reponse, EntityManagerInterface $entityManager)
    {
        $entityManager->remove($reponse);
        $entityManager->flush();
    
        return $this->redirectToRoute('reponse');
    }

    /** PARTIE JSON */
    #[Route("repdisplayJSON", name:"reponse_displayJSON")]
    public function displayJSON(ReponseRepository $repo, NormalizerInterface $normalizer)
    {
        $reponse = $repo->findAll();
        $reponseNormalises = $normalizer->normalize($reponse, 'json', ['groups' => "reponse"]);
        $json= json_encode($reponseNormalises);
        return new Response($json);
    }

    #[Route("repaddJSON", name:"reponse_addJSON")]
    public function addJSON(Request $req, NormalizerInterface $Normalizer)
    {
        $em = $this->getDoctrine()->getManager();
        $reponse = new Reponse();
        $reponse->setIdUser(intval($req->get('IdUser') ?? 0));
        $reponse->setNote($req->get('note'));
        $createdAt = new \DateTime($req->get('createdAt'));
        $reponse->setCreatedAt($createdAt);
        $em->persist($reponse);
        $em->flush();

        $jsonContent = $Normalizer->normalize($reponse, 'json', ['groups' => 'reponse']);
        return new Response(json_encode($jsonContent));
    }

    #[Route("repeditJSON/{id}", name:"reponse_editJSON")]
    public function editJSON(Request $req, $id, NormalizerInterface $Normalizer)
    {
        $em = $this->getDoctrine()->getManager();
        $reponse = $em->getRepository(Reponse::class)->find($id);
        $reponse->setIdUser(intval($req->get('IdUser') ?? 0));
        $reponse->setNote($req->get('note') ?? '');
        $createdAt = new \DateTime($req->get('createdAt'));
        $reponse->setCreatedAt($createdAt);
        $em->flush();

        $jsonContent = $Normalizer->normalize($reponse, 'json', ['groups' => 'reponse']);
        return new Response("Reponse updated successfully" . json_encode($jsonContent));
    }

    #[Route("repdeleteJSON/{id}", name:"reponse_deleteJSON")]
    public function deleteJSON(Request $req, $id, NormalizerInterface $Normalizer)
    {
        $em = $this->getDoctrine()->getManager();
        $reponse = $em->getRepository(Reponse::class)->find($id);
        $em->remove($reponse);
        $em->flush();

        $jsonContent = $Normalizer->normalize($reponse, 'json', ['groups' => 'reponse']);
        return new Response("Reponse deleted successfully" . json_encode($jsonContent));
    }
   
}