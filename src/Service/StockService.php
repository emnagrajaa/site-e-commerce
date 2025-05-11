<?php

namespace App\Service;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;

class StockService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function checkStock(Product $product, int $quantity): bool
    {
        return $product->getStock() >= $quantity;
    }

    public function updateStock(Product $product, int $quantity): void
    {
        $product->setStock($product->getStock() - $quantity);
        $this->entityManager->persist($product);
        $this->entityManager->flush();
    }
}