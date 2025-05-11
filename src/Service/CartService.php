<?php

namespace App\Service;

use App\Entity\Product;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function addProduct(Product $product, int $quantity = 1): void
    {
        $cart = $this->session->get('cart', []);
        $cart[$product->getId()] = ($cart[$product->getId()] ?? 0) + $quantity;
        $this->session->set('cart', $cart);
    }

    public function getTotal(array $products): float
    {
        $total = 0;
        $cart = $this->session->get('cart', []);
        foreach ($cart as $productId => $quantity) {
            $product = current(array_filter($products, fn($p) => $p->getId() == $productId));
            $total += $product->getPrice() * $quantity;
        }
        return $total;
    }

    public function clear(): void
    {
        $this->session->set('cart', []);
    }
}