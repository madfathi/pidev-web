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


class AjaxController extends AbstractController
{
    #[Route('/backrechercheAjaxs', name: 'backrechercheAjaxs')]
public function searchAjax(Request $request, ClientRepository $clientRepository): JsonResponse
{
    $name = $request->get('nom');
    
    // Récupérer les clients filtrés par nom
    $filteredClients = $clientRepository->findByNameLike($name);

    // Préparer les données des clients filtrés à retourner
    $formattedClients = [];
    foreach ($filteredClients as $client) {
        $formattedClients[] = [
            'id' => $client->getIdC(),
            'nom' => $client->getNom(),
            'prenom' => $client->getPrenom(),
            'age' => $client->getAge(),
            'poids' => $client->getPoids(),
            'hauteur' => $client->getHauteur(),
        ];
    }
    
    // Retourner les données des clients filtrés au format JSON
    return new JsonResponse(['clients' => $formattedClients]);
}

  
}
