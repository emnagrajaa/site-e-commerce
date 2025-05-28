<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Product;
use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route('/cart')]
class CartController extends AbstractController
{
    private $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    #[Route('/', name: 'app_cart_index', methods: ['GET'])]
    public function index(): Response
    {
        $user = $this->getUser();
        if (!$user instanceof UserInterface) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour voir votre panier.');
        }

        $cartData = $this->cartService->getDetailedCart(); // Updated: No arguments needed
        $cartItems = $cartData['items'];
        $total = $cartData['total'];

        dump($cartItems); // Kept for debugging

        return $this->render('cart/index.html.twig', [
            'cartItems' => $cartItems,
            'total' => $total,
        ]);
    }

    #[Route('/add/{id}', name: 'app_cart_add', methods: ['POST'])]
    public function add(Request $request, Product $product): Response
    {
        $user = $this->getUser();
        if (!$user instanceof UserInterface) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour ajouter au panier.');
        }

        $quantity = (int) $request->request->get('quantity', 1);
        $this->cartService->addProduct($product, $quantity);

        $this->addFlash('success', 'Produit ajouté au panier !');
        return $this->redirectToRoute('app_cart_index');
    }

    #[Route('/remove/{id}', name: 'app_cart_remove', methods: ['POST'])]
    public function remove(Product $product): Response
    {
        $user = $this->getUser();
        if (!$user instanceof UserInterface) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour modifier votre panier.');
        }

        $this->cartService->removeProduct($product); // Updated: No user argument

        $this->addFlash('success', 'Produit retiré du panier !');
        return $this->redirectToRoute('app_cart_index');
    }

    #[Route('/update/{id}/{quantity}', name: 'app_cart_update', methods: ['GET'])]
    public function update(Product $product, int $quantity): Response
    {
        $user = $this->getUser();
        if (!$user instanceof UserInterface) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour modifier votre panier.');
        }

        if ($quantity <= 0) {
            $this->cartService->removeProduct($product);
        } else {
            $this->cartService->updateQuantity($product, $quantity); // Updated: No user argument
        }

        $this->addFlash('success', 'Quantité mise à jour !');
        return $this->redirectToRoute('app_cart_index');
    }

    #[Route('/clear', name: 'app_cart_clear', methods: ['POST'])]
    public function clear(): Response
    {
        $user = $this->getUser();
        if (!$user instanceof UserInterface) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour vider votre panier.');
        }

        $this->cartService->clear(); // Updated: No user argument, fixed syntax

        $this->addFlash('success', 'Panier vidé avec succès !');
        return $this->redirectToRoute('app_cart_index');
    }
}