<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\ClientType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
#[Route('/clientf')]
class ClientfController extends AbstractController
{


#[Route('/newf', name: 'app_client_newF', methods: ['GET', 'POST'])]
    public function newF(Request $request, EntityManagerInterface $entityManager): Response
    {
        $client = new Client();
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);
        $user_id = $this->get('session')->get('user_id');
        $user = $entityManager->getRepository(User::class)->find($user_id);
    
        if ($form->isSubmitted() && $form->isValid()) {
                        // zid client.id= session id  bech ywali yasna3 client 3ando nafs el id que el user el connecté + test unicité ( user yasna3 client we7ed barka)

            $entityManager->persist($client);
            $entityManager->flush();

            return $this->redirectToRoute('app_program_indexf', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('client/newF.html.twig', [
            'client' => $client,
            'form' => $form,
            'user' => $user,
        ]);
    }

}