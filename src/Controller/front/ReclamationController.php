<?php

namespace App\Controller\front;

use App\Entity\Reclamation;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Form\ReclamationFormType;
use App\Repository\ReclamationRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;
use Dompdf\Dompdf;
use Dompdf\Options;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;



class ReclamationController extends AbstractController
{
    #[Route('/reclamation', name: 'reclamation')]
public function index(ReclamationRepository $repository, Request $request, PaginatorInterface $paginator): Response
{
    // Retrieve reclamations from the repository
    $reclamations = $repository->findAll();

    // Check if the request is AJAX
    if ($request->isXmlHttpRequest()) {
        // Render the reclamations as HTML
        $html = $this->renderView("reclamation/front/reclamation_index.html.twig", [
            "reclamations" => $reclamations,
        ]);

        // Return the HTML response
        return new JsonResponse($html);
    } else {
        // Render the reclamations as HTML
        return $this->render("reclamation/front/reclamation_index.html.twig", [
            "reclamations" => $reclamations,
        ]);
    }
}




    #[Route('/reclamation_new', name: 'reclamation_new')]
    public function new(Request $request, SessionInterface $session, ReclamationRepository $reclamationRepository, EntityManagerInterface $entityManager): Response
    {
        $reclamation = new Reclamation();
        $form = $this->createForm(ReclamationFormType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $reclamation->setCreatedAt(new \DateTime('now'));
            $tel = $form->get('tel')->getData();
            $phoneUtil = PhoneNumberUtil::getInstance();
            // $user = $entityManager
            //     ->getRepository(User::class)
            //     ->findBy(['email' => $reclamation->getEmail()])[0];
            // $reclamation->setUserid($user);
            // Temporarily commented out to remove dependency on User
            $numberProto = $phoneUtil->parse($tel, 'TN'); // Replace "FR" with the ISO code of the country of the entered phone number
            $formattedTel = $phoneUtil->format($numberProto, PhoneNumberFormat::E164); // The E164 format includes the country code

            // Create a new reclamation with the formatted phone number
            $reclamation->setTel($formattedTel);
            $file = $form->get('file')->getData();
            if ($file) {
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                $file->move($this->getParameter('files_directory'), $fileName);
                $reclamation->setFile($fileName);
            }
            $entityManager->persist($reclamation);
            $entityManager->flush();

            // Add a notification for ongoing reclamations
            $reclamations = $reclamationRepository->findBy(['statut' => 'En cours']);
            foreach ($reclamations as $reclamation) {
                $session->getFlashBag()->add('success', [
                    'message' => 'The reclamation "' . $reclamation->getReference() . '" is being processed.',
                    'dismissable' => true,
                ]);
            }
            return $this->redirectToRoute('reclamation');
        }

        return $this->renderForm('reclamation/front/new.html.twig', ['form' => $form]);
    }

    #[Route('/reclamation_edit/{id}', name: 'reclamation_edit')]
    public function edit(Request $request, int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $reclamation = $entityManager->getRepository(Reclamation::class)->find($id);
        $originalFile = $reclamation->getFile(); // store the original file filename
        $form = $this->createForm(ReclamationFormType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('file')->getData(); // get the uploaded file

            if ($file) {
                // generate a unique filename
                $newFilename = md5(uniqid()) . '.' . $file->guessExtension();

                // move the file to the files directory
                $file->move(
                    $this->getParameter('files_directory'),
                    $newFilename
                );

                // update the entity with the new filename
                $reclamation->setFile($newFilename);

                // delete the original file, if it exists
                if ($originalFile) {
                    $originalFilePath = $this->getParameter('files_directory') . '/' . $originalFile;
                    if (file_exists($originalFilePath)) {
                        unlink($originalFilePath);
                    }
                }
            } else {
                // use the original file filename
                $reclamation->setFile($originalFile);
            }

            $reclamation->setCreatedAt(new \DateTime('now'));

            // save and execute the changes to the database
            $entityManager->persist($reclamation);
            $entityManager->flush();
            $flush = $this->addFlash('successmod', 'Reclamation was successfully updated.');
            return $this->redirectToRoute('reclamation');
        }

        return $this->renderForm('reclamation/front/new.html.twig', ['form' => $form]);
    }

    #[Route('/reclamation_delete/{id}', name: 'reclamation_delete')]
    public function delete(int $id, EntityManagerInterface $entityManager): Response
{
    // Fetch the Reclamation entity by its ID
    $reclamation = $entityManager->getRepository(Reclamation::class)->find($id);

    // Check if the reclamation exists
    if (!$reclamation) {
        throw $this->createNotFoundException('Reclamation not found.');
    }

    // Remove the reclamation
    $entityManager->remove($reclamation);
    $entityManager->flush();

    // Add a flash message to indicate successful deletion
    $this->addFlash('successsupp', 'Reclamation was successfully deleted.');

    // Redirect to the reclamation index page
    return $this->redirectToRoute('reclamation');
}

    #[Route('/orderByDateDESC', name: 'app_reclamation_orderByDateDESC')]
    public function orderByDateDESC(Request $request, ReclamationRepository $reclamationRepository, PaginatorInterface $paginator): Response
    {
        $reclamation = $reclamationRepository->orderByDateDESC();
        $page = $request->query->getInt('page', 1);
        $form = $this->createForm(ReclamationFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $query = $form->getData()['query'];
            if ($query == "") {
                $reclamation = $reclamationRepository->findAll();
            } else {
                $reclamation = $reclamationRepository->searchProduct($query);
            }
        }
        $reclamation = $paginator->paginate($reclamation, $page, 3);
        return $this->render('reclamation/front/reclamation_index.html.twig', [
            'reclamations' => $reclamation,
            'form' => $form->createView(),

        ]);
    }

    #[Route('/orderByDateASC', name: 'app_reclamation_orderByDateASC')]
    public function orderByDateASC(Request $request, ReclamationRepository $reclamationRepository, PaginatorInterface $paginator): Response
    {
        $reclamation = $reclamationRepository->orderByDateASC();
        $page = $request->query->getInt('page', 1);
        $form = $this->createForm(ReclamationFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $query = $form->getData()['query'];
            if ($query == "") {
                $reclamation = $reclamationRepository->findAll();
            } else {
                $reclamation = $reclamationRepository->searchProduct($query);
            }
        }
        $reclamation = $paginator->paginate($reclamation, $page, 3);
        return $this->render('reclamation/front/reclamation_index.html.twig', [
            'reclamations' => $reclamation,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/sms/{id}', name: 'app_sms')]
    function envoyerSMS(ReclamationRepository $repository, $id, Request $request, ManagerRegistry $doctrine)
    {
        $reclamation = $repository->find($id);
        if (!$reclamation) {
            throw $this->createNotFoundException('Reclamation non trouvée.');
        }

        // Récupération du numéro de téléphone du client
        $tel = $reclamation->getTel();
        if (!$tel) {
            throw $this->createNotFoundException('Numéro de téléphone non trouvé.');
        }

        // Envoi du SMS
        $repository->sms($tel);

        $em = $doctrine->getManager();
        $em->flush();

        $this->addFlash('danger', 'SMS envoyé avec succès');

        return $this->redirectToRoute('reclamation');
    }

    #[Route('/pdf', name: 'PDF_Reclamation')]
    public function pdf(ReclamationRepository $Repository)
    {
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Open Sans');

        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('reclamation/front/pdf.html.twig', [
            'reclamations' => $Repository->findAll(),
        ]);

        // Add header HTML to $html variable
        $headerHtml = '<h1 style="text-align: center; color: #b00707;">Liste des reclamations</h1>';
        $html = $headerHtml . $html;

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A3', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();
        // Output the generated PDF to Browser (inline view)
        $dompdf->stream("ListeDesReclamations.pdf", [
            "reclamations" => true
        ]);
    }
    #[Route('/rechercheAjax', name: 'rechercheAjax')]
    public function searchAjax(Request $request, ReclamationRepository $repo, PaginatorInterface $paginator)
    {
        // Récupérez le paramètre de recherche depuis la requête
        $query = $request->query->get('q');
        // Récupérez les plats correspondants depuis la base de données
        $reclamation = $paginator->paginate(
            $repo->findReclamationByRef($query), /* query NOT result */
            $request->query->getInt('page', 1),
            3
        );
        $html =  $this->renderView("reclamation/front/reclamation_index.html.twig", [
            "reclamations" => $reclamation,
        ]);

        return new Response($html);
    }


    /** PARTIE JSON */
    #[Route("/recdisplayJSON", name: "reclamation_displayJSON")]
    public function displayJSON(ReclamationRepository $repo, NormalizerInterface $normalizer)
    {
        $reclamation = $repo->findAll();
        $reclamationNormalises = $normalizer->normalize($reclamation, 'json', ['groups' => "reclamation"]);
        $json = json_encode($reclamationNormalises);
        return new Response($json);
    }

    #[Route("/recaddJSON", name: "reclamation_addJSON")]
    public function addJSON(Request $req, NormalizerInterface $Normalizer)
    {
        $em = $this->getDoctrine()->getManager();

        // Récupération de la référence à insérer
        $reference = $req->get('reference');

        // Vérification si la référence existe déjà dans la base de données
        $reclamationExists = $em->getRepository(Reclamation::class)->findOneBy(['reference' => $reference]);

        // Si la réclamation existe déjà, retourner un message d'erreur
        if ($reclamationExists) {
            return new Response('La réclamation existe déjà.', 409);
        }

        // Si la réclamation n'existe pas encore, créer une nouvelle réclamation
        $reclamation = new Reclamation();

        if (isset($reference)) {
            $reclamation->setReference($reference);
        }

        $reclamation->setNomD($req->get('nomD') ?? '');
        $reclamation->setPrenomD($req->get('prenomD') ?? '');
        $reclamation->setCin(intval($req->get('cin') ?? 0));
        $reclamation->setEmail($req->get('email') ?? '');
        $reclamation->setCommentaire($req->get('commentaire') ?? '');
        $createdAt = new \DateTime($req->get('createdAt') ?? '');
        $reclamation->setCreatedAt($createdAt);
        $reclamation->setStatut($req->get('statut') ?? '');
        $reclamation->setFile($req->get('file') ?? '');
        $reclamation->setTel($req->get('tel') ?? '');
        $em->persist($reclamation);
        $em->flush();

        // Retourner la réponse JSON avec les données de la nouvelle réclamation
        $jsonContent = $Normalizer->normalize($reclamation, 'json', ['groups' => 'reclamation']);
        return new Response(json_encode($jsonContent));
    }

    #[Route("/receditJSON/{id}", name: "reclamation_editJSON")]
    public function editJSON(Request $req, $id, NormalizerInterface $Normalizer)
    {
        $em = $this->getDoctrine()->getManager();
        // Récupération de la référence à insérer
        $reference = $req->get('reference');

        // Vérification si la référence existe déjà dans la base de données
        $reclamationExists = $em->getRepository(Reclamation::class)->findOneBy(['reference' => $reference]);

        $reclamation = $em->getRepository(Reclamation::class)->find($id);
        if (isset($reference) && !$reclamationExists) { // vérification de l'existence de la référence dans la base de données
            $reclamation->setReference($reference);
        }

        $reclamation->setNomD($req->get('nomD') ?? '');
        $reclamation->setPrenomD($req->get('prenomD') ?? '');
        $reclamation->setCin(intval($req->get('cin') ?? 0));
        $reclamation->setEmail($req->get('email') ?? '');
        $reclamation->setCommentaire($req->get('commentaire') ?? '');
        $createdAt = new \DateTime($req->get('createdAt') ?? '');
        $reclamation->setCreatedAt($createdAt);
        $reclamation->setStatut($req->get('statut') ?? '');
        $reclamation->setFile($req->get('file') ?? '');
        $reclamation->setTel($req->get('tel') ?? '');
        $em->flush();

        $jsonContent = $Normalizer->normalize($reclamation, 'json', ['groups' => 'reclamation']);
        return new Response("Reclamation updated successfully" . json_encode($jsonContent));
    }

    #[Route("/recdeleteJSON/{id}", name: "reclamation_deleteJSON")]
    public function deleteJSON(Request $req, $id, NormalizerInterface $Normalizer)
    {
        $em = $this->getDoctrine()->getManager();
        $reclamation = $em->getRepository(Reclamation::class)->find($id);
        $em->remove($reclamation);
        $em->flush();

        $jsonContent = $Normalizer->normalize($reclamation, 'json', ['groups' => 'reclamation']);
        return new Response("Reclamation deleted successfully" . json_encode($jsonContent));
    }
}
