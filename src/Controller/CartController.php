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

        $cart = $this->cartService->getCart($user);
        $cartItems = $this->cartService->getCartItems($cart);
        $total = $this->cartService->getTotal($cart);

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
        $this->cartService->addProduct($user, $product, $quantity);

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

        $this->cartService->removeProduct($user, $product);

        $this->addFlash('success', 'Produit retiré du panier !');
        return $this->redirectToRoute('app_cart_index');
    }
}