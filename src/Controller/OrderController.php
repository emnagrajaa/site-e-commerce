<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Service\CartService;
use App\Service\EmailService;
use App\Service\StockService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductRepository; // Ensure this is imported

#[Route('/order')]
class OrderController extends AbstractController
{
    #[Route('/create', name: 'app_order_create', methods: ['POST'])]
    public function create(CartService $cartService, StockService $stockService, EmailService $emailService, EntityManagerInterface $em, ProductRepository $productRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $cart = $cartService->getCart();
        $products = $productRepository->findBy(['id' => array_keys($cart)]);

        $order = new Order();
        $order->setUser($this->getUser());
        $order->setTotal($cartService->getTotal()); // Updated to match CartService method

        foreach ($cart as $productId => $quantity) {
            $product = array_filter($products, fn($p) => $p->getId() == $productId)[0];
            if (!$stockService->checkStock($product, $quantity)) {
                throw $this->createAccessDeniedException('Stock insuffisant');
            }
            $orderItem = new OrderItem();
            $orderItem->setProduct($product);
            $orderItem->setQuantity($quantity);
            $order->addOrderItem($orderItem);
            $stockService->updateStock($product, $quantity);
        }

        $em->persist($order);
        $em->flush();

        $emailService->sendOrderConfirmation($this->getUser()->getEmail(), $order);
        $cartService->clear();

        return $this->redirectToRoute('app_order_confirmation'); // Redirect to a confirmation page
    }

    #[Route('/', name: 'app_order_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('order/index.html.twig');
    }

    #[Route('/history', name: 'app_order_history', methods: ['GET'])]
    public function history(): Response
    {
        return $this->render('order/history.html.twig', [
            'orders' => $this->getUser()->getOrders(),
        ]);
    }

    #[Route('/checkout', name: 'app_checkout', methods: ['GET','POST'])]
    public function checkout(CartService $cartService, StockService $stockService, EmailService $emailService, EntityManagerInterface $em, ProductRepository $productRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $cart = $cartService->getCart();
        $products = $productRepository->findBy(['id' => array_keys($cart)]);

        if (empty($cart)) {
            $this->addFlash('danger', 'Votre panier est vide.');
            return $this->redirectToRoute('app_cart_index');
        }

        $order = new Order();
        $order->setUser($this->getUser());
        $order->setTotal($cartService->getTotal()); // Updated to match CartService method

        foreach ($cart as $productId => $quantity) {
            $product = array_filter($products, fn($p) => $p->getId() == $productId)[0];
            if (!$product) {
                $this->addFlash('danger', 'Produit introuvable.');
                return $this->redirectToRoute('app_cart_index');
            }
            if (!$stockService->checkStock($product, $quantity)) {
                $this->addFlash('danger', 'Stock insuffisant pour un produit.');
                return $this->redirectToRoute('app_cart_index');
            }
            $orderItem = new OrderItem();
            $orderItem->setProduct($product);
            $orderItem->setQuantity($quantity);
            $order->addOrderItem($orderItem);
            $stockService->updateStock($product, $quantity);
        }

        $em->persist($order);
        $em->flush();

        $emailService->sendOrderConfirmation($this->getUser()->getEmail(), $order);
        $cartService->clear();

        $this->addFlash('success', 'Commande passée avec succès !');
        return $this->redirectToRoute('app_order_confirmation'); // Redirect to a confirmation page
    }
    #[Route('/confirmation', name: 'app_order_confirmation', methods: ['GET'])]
    public function confirmation(): Response
    {
        return $this->render('order/confirmation.html.twig');
    }

}