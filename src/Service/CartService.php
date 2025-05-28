<?php

namespace App\Service;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{
    private SessionInterface $session;
    private EntityManagerInterface $entityManager;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->session = $requestStack->getSession();
        $this->entityManager = $entityManager;
    }

    public function addProduct(Product $product, int $quantity = 1): void
    {
        // Vérifie si le produit existe et a assez de stock
        if (!$product) {
            throw new \InvalidArgumentException('Produit non trouvé.');
        }

        $currentQuantity = $this->getQuantity($product);
        $newQuantity = $currentQuantity + $quantity;

        if ($product->getStock() < $newQuantity) {
            throw new \LogicException('Stock insuffisant pour ajouter ce produit.');
        }

        $cart = $this->session->get('cart', []);
        $cart[$product->getId()] = $newQuantity;
        $this->session->set('cart', $cart);
    }

    public function removeProduct(Product $product): void
    {
        $cart = $this->session->get('cart', []);
        if (isset($cart[$product->getId()])) {
            unset($cart[$product->getId()]);
            $this->session->set('cart', $cart);
        }
    }

    public function updateQuantity(Product $product, int $quantity): void
    {
        if ($quantity <= 0) {
            $this->removeProduct($product);
            return;
        }

        if ($product->getStock() < $quantity) {
            throw new \LogicException('Stock insuffisant pour cette quantité.');
        }

        $cart = $this->session->get('cart', []);
        if (isset($cart[$product->getId()])) {
            $cart[$product->getId()] = $quantity;
            $this->session->set('cart', $cart);
        }
    }

    public function clear(): void
    {
        $this->session->set('cart', []);
    }

    public function hasProduct(Product $product): bool
    {
        $cart = $this->session->get('cart', []);
        return isset($cart[$product->getId()]);
    }

    public function getQuantity(Product $product): int
    {
        $cart = $this->session->get('cart', []);
        return $cart[$product->getId()] ?? 0;
    }

    public function getCart(): array
    {
        return $this->session->get('cart', []);
    }

    public function getDetailedCart(): array
    {
        $cart = $this->getCart();
        $detailedCart = [];
        $total = 0;

        foreach ($cart as $productId => $quantity) {
            $product = $this->entityManager->getRepository(Product::class)->find($productId);
            if ($product) {
                $subtotal = floatval($product->getPrice()) * $quantity;
                $total += $subtotal;
                $detailedCart[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'subtotal' => $subtotal,
                ];
            } else {
                // Supprimer les produits invalides du panier
                unset($cart[$productId]);
                $this->session->set('cart', $cart);
            }
        }

        return [
            'items' => $detailedCart,
            'total' => $total,
        ];
    }

    public function getTotal(): float
    {
        $cartDetails = $this->getDetailedCart();
        return $cartDetails['total'];
    }
}