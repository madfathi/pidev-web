<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileFormType;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\RegistrationController;

use App\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Dompdf\Dompdf;
use Dompdf\Options;

use Symfony\Component\Security\Core\Security;


#[Route('/user')]
class UserController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }




    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(Request $request , UserRepository $userRepository, PaginatorInterface $paginator): Response
    {
        $users = $userRepository->findAll();
        $users =  $paginator->paginate(
            $users ,
            $request->query->getInt('page', 1), // Notez la virgule ajoutée ici
            10
        );
        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }
    #[Route('/delete/{id}', name: 'delete')]
    public function supprimerCategory($id): Response
    {
        $donne=$this->getDoctrine()->getRepository(User::class)->find($id);
        $em=$this->getDoctrine()->getManager();
        $em->remove($donne);
        $em->flush();
        return $this->redirectToRoute('app_user_home');


       
    }
    
    #[Route('/TriPAB', name: 'app_tri_nom')]
            public function Tri(  UserRepository $userRepository)
            {
                $donne =  $userRepository->orderByNomASC();
                return $this->render("user/base.html.twig", array( 'donne' => $donne));
            }
        #[Route('/{id}/generate-pdf', name: 'contrat_generate_pdf')]
            public function generatePdf($id): Response
            {
                // Fetch the Commande entity by its ID
                $entityManager = $this->getDoctrine()->getManager();
                $user = $entityManager->getRepository(User::class)->find($id);
        
                if (!$user) {
                    throw $this->createNotFoundException('Commande not found for ID ' . $id);
                }
        
                // Get the HTML content of the page you want to convert to PDF
                $html = $this->renderView('user/show_pdf.html.twig', [
                    'user' => $user,
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
    #[Route('/backrechercheAjax', name: 'backrechercheAjax')]
    public function searchAjax(Request $request,UserRepository $userRepository): Response
    {
        $query = $request->query->get('q');
        $donne = $userRepository->findByCommandeByNom($query); // Adjust this method according to your actual search logic in CommandeRepository

        return $this->render('user/base.html.twig', [
            'donne' => $donne,
        ]);
    }

    #[Route('/home', name: 'app_user_home')]
    public function indexHome(): Response
    {
        $em=$this->getDoctrine()->getRepository(User::class);
        $donne=$em->findAll();
        return $this->render('user/base.html.twig', [
            'donne' => $donne,
        ]);
       
    }
    #[Route('/homee', name: 'app_user_homee')]
    public function indexHome2(): Response
    {
        $em=$this->getDoctrine()->getRepository(User::class);
        $users=$em->findAll();
        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
       
    }

    #[Route('/forUser', name: 'app_user_forUser')]
    public function indexUser(): Response
    {

        return $this->render('user/forUser.html.twig');
    }
    public function profile2(Request $request, Security $security): Response
    {
        // Récupérer l'ID de l'utilisateur à partir de la session
        $userId = $request->getSession()->get('user_id');

        if (!$userId) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        // Charger les données de l'utilisateur à partir de la base de données
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->find($userId);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        // Afficher les données de l'utilisateur dans un template
        return $this->render('user/profile.html.twig', [
            'user' => $user,
          
        ]);
       
    }


    #[Route('/{id}/profile', name: 'app_profile', methods: ['GET', 'POST'])]
    public function profile(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProfileFormType::class, $user);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
    
            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->render('user/profile.html.twig', [
            'user' => $this->getUser(),
            'form' => $form->createView(),
        ]);
    }


    #[Route('/profile/{id}/edit', name: 'profile_edit', methods: ['GET', 'POST'])]
    public function editProfile(Request $request, $id)
    {
        // Récupérer l'utilisateur à partir de son ID
        $user = $this->entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException('No user found for id '.$id);
        }

        // Créer le formulaire de type ProfileFormType et associer les données de l'utilisateur
        $form = $this->createForm(ProfileFormType::class, $user);

        // Gérer la soumission du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Mettre à jour les données de l'utilisateur dans la base de données
            $this->entityManager->flush();

            // Rediriger l'utilisateur vers une autre page après la soumission du formulaire
            return $this->redirectToRoute('profile_show', ['id' => $user->getId()]);
        }

        return $this->render('profile/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }



   
    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    public function showUserAction(User $user)
{
    return $this->render('base.html.twig', [
        'user' => $user,
    ]);
}

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(int $id, UserRepository $userRepository): Response
    {
    $userRepository = $this->entityManager->getRepository(User::class);  
    $id = (int) $id; // Convert the string ID to an integer
    $user = $userRepository->find($id); // Replace $userId with the actual ID of the user you want to retrieve
    if (!$user) {
        throw $this->createNotFoundException('User not found');
    }
    return $this->render('user/show.html.twig', [
        'user' => $user,
    ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/statous', name: 'statip')]
    public function statistiques(){
      
        return $this->render('user/statistique.html.twig');
    }

    public function statistics(): Response
    {
        $repository = $this->getDoctrine()->getRepository(user::class);
        $users = $repository->findAll();
    
        // Initialiser un tableau pour stocker les statistiques
        $statistics = [];
    
        // Calculer le nombre de séances par type
        foreach ($users as $user) {
            $roles = $user->getRoles();
            if (!isset($statistics[$roles])) {
                $statistics[$roles] = 0;
            }
            $statistics[$roles]++;
        }
    
        // Renvoyer les statistiques au format JSON
        return $this->json($statistics);
    }

    #[Route('/fetch_users', name: 'fetch_users')]
    public function fetchUsers(): Response
    {
        $users = $this->getDoctrine()->getRepository(user::class)->findAll();
    
        $usersData = [];
        foreach ($users as $user) {
            $usersData[] = [
                'roles' => $user->getRoles(),
                'nom' => $user->getNom(),
                'prenom' => $user->getPrenom(),
                'cin' => $user->getCin(),
            
            ];
        }
    
        return $this->json($usersData);
    }
}
