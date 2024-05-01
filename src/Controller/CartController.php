<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Panier;
use App\Entity\Produits;
use App\Repository\ProduitsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\SmsGenerator;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart')]
    public function index(Request $request){
        $cart = $request->getSession()->get('cart', []);
       
        return $this->render('cart/index2.html.twig', [
            'cart' => $cart,
        ]);

    

        
    }
    #[Route('/cart_add/{id}', name: 'cart_add')]

    public function addToCart($id, Request $request, EntityManagerInterface $entityManager)
    {
        $product = $this->getDoctrine()->getRepository(Produits::class)->find($id);

        if (!$product) {
            throw $this->createNotFoundException('Product not found');
        }
    
        $quantity = $request->request->get('quantity', 1); // Récupère la quantité depuis le formulaire
    
        $cart = $request->getSession()->get('cart', []);
        $totalCart = $request->getSession()->get('totalCart', $product->getPrix());
    
        // Vérifie si le produit existe déjà dans le panier
        if (isset($cart[$id])) {
            // Calculer le montant total avant la mise à jour du produit dans le panier
            $previousTotal = $cart[$id]['quantity'] * $product->getPrix();
    
            // Met à jour la quantité du produit dans le panier
            $cart[$id]['quantity'] += $quantity;
    
            // Calculer le nouveau montant total du produit dans le panier
            $newTotal = $cart[$id]['quantity'] * $product->getPrix();
            $cart[$id]['total'] = $newTotal;
    
            // Met à jour le total général du panier en ajoutant la différence entre le nouveau total et l'ancien total
            $totalCart += ($newTotal - $previousTotal);
        } else {
            // Ajoute le produit au panier
            $cart[$id] = [
                'id' => $product->getId(),
                'name' => $product->getNom(),
                'price' => $product->getPrix(),
                'quantity' => $quantity,
                'total' => $quantity * $product->getPrix(),
            ];
        }
    
        // Mettre à jour le panier en session
        $request->getSession()->set('cart', $cart);
        $request->getSession()->set('totalCart', $totalCart);
    
        // Enregistrer les éléments du panier dans la base de données
        foreach ($cart as $productId => $item) {
            $panier = new Panier();
            $panier->setProdId($item['id']);
            $panier->setNomp($item['name']);
            $panier->setPt($item['price']);
            $panier-> setQuantite($item['quantity']);
            $panier-> setImg("");
           
    
            // Associer éventuellement le panier à l'utilisateur (si vous avez un champ userId dans votre entité Panier)
            // $panier->setUserId($userId);
    
            $entityManager->persist($panier);
        }
    
        $entityManager->flush();
    
   

    // Mettre à jour le panier en session
    $request->getSession()->set('cart', $cart);
    $request->getSession()->set('totalCart', $totalCart);


    return $this->redirectToRoute('app_cart');
}

#[Route('/cart_remove/{id}', name: 'cart_remove')]
public function remove(int $id,Request $request): Response
    {
        // Récupérer le produit depuis la base de données
        $product = $this->getDoctrine()->getRepository(Produits::class)->find($id);

        if (!$product) {
            throw $this->createNotFoundException('Produit non trouvé.');
        }

        // Récupérer le panier depuis la session
        $cart = $request->getSession()->get('cart', []);
    
        if (isset($cart[$id])) {
            // Décrémenter la quantité du produit
            $cart[$id]['quantity']--;

            // Mettre à jour le total du panier en fonction de la nouvelle quantité
            $prixProduit =  $cart[$id]['price']; // Prix unitaire du produit
            $cart[$id]['total'] = $prixProduit *  $cart[$id]['quantity'];

            // Si la quantité atteint zéro, retirer le produit du panier
            if ($cart[$id]['quantity'] <= 0) {
                unset( $cart[$id]);
            }

            // Mettre à jour le montant total du panier
            $totalCart = 0;
            foreach ( $cart as $item) {
                $totalCart += $item['total'];
            }

            // Mettre à jour le panier dans la session
            $request->getSession()->set('cart', $cart);
            $request->getSession()->set('totalCart', $totalCart);
        }
    // Redirect to the cart page
    return $this->redirectToRoute('app_cart');
    }
    #[Route('/create_order', name: 'create_order')]
public function createOrder(Request $request, EntityManagerInterface $entityManager,SmsGenerator $smsGenerator)
{
    $cart = $request->getSession()->get('cart', []);
    // Récupérer les informations de l'utilisateur depuis le formulaire (nom, prénom, email)
    $nom =('khaled');
    $prenom = ('tebourbi');
    $email =('mohamedkhaled.tebourbi@esprit.tn');
    $panier= $cart;
    $tel =93237722; 
    $number=93237722;
    $addr =('zahra');
    $text=  ('Votre commande a ete bien passe !!');

    // Récupérer le panier depuis la session
   

    // Créer une nouvelle instance de Commande
    $commande = new Commande();
    $commande->setNom($nom);
    $commande->setPre($prenom);
    $commande->setMail($email);
    $commande->setPani($panier);
    $commande->setAddr( $addr);
    $commande->setTel(  $tel);
  
    $entityManager->persist( $commande);
    $entityManager->flush();
   
    $number_test=$_ENV['twilio_to_number'];// Numéro vérifier par twilio. Un seul numéro autorisé pour la version de test.
    

    //Appel du service
    $smsGenerator->sendSms($number_test ,$nom,$text);


    // Mettre à jour le panier dans la session
    $request->getSession()->set('cart', []);
    $request->getSession()->set('totalCart', 0);

    // Rediriger vers une page de confirmation de commande ou toute autre page appropriée
    return $this->redirectToRoute('app_cart');
}
#[Route('/cart_clear', name: 'cart_clear')]
public function clearCart(Request $request): Response
{
    // Supprimer toutes les données de la session liées au panier
    $request->getSession()->remove('cart');
    $request->getSession()->remove('totalCart');

    // Redirection vers la page du panier (ou toute autre page souhaitée)
    return $this->redirectToRoute('app_cart');
}

}