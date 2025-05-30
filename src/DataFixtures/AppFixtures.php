<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{

    public function load(ObjectManager $manager): void
    {

        // Vérifier et ajouter des catégories
        $categoriesData = [
            'Vêtements Traditionnels',
            'Accessoires',
        ];
        $categories = [];
        foreach ($categoriesData as $catName) {
            $existingCategory = $manager->getRepository(Category::class)->findOneBy(['name' => $catName]);
            if (!$existingCategory) {
                $category = new Category();
                $category->setName($catName);
                $manager->persist($category);
                $categories[$catName] = $category;
            } else {
                $categories[$catName] = $existingCategory;
            }
        }

        // Vérifier et ajouter des produits basés sur les images
        $products = [
            [
                'name' => 'Robe Rouge Traditionnelle',
                'description' => 'Élégante robe rouge ornée de broderies dorées, parfaite pour les occasions spéciales.',
                'price' => '149.99',
                'stock' => 5,
                'image' => 'robe_rouge.png',
                'categoryName' => 'Vêtements Traditionnels',
            ],
            [
                'name' => 'Ensemble Enfant Bleu',
                'description' => 'Ensemble traditionnel bleu pour enfants avec broderies blanches, accompagné de chapeaux rouges.',
                'price' => '59.99',
                'stock' => 10,
                'image' => 'ensemble_enfant_bleu.png',
                'categoryName' => 'Vêtements Traditionnels',
            ],
            [
                'name' => 'Vêtement Masculin Blanc',
                'description' => 'Vêtement traditionnel masculin blanc avec motifs dorés, idéal pour les cérémonies.',
                'price' => '129.99',
                'stock' => 7,
                'image' => 'vetement_masculin_blanc.png',
                'categoryName' => 'Vêtements Traditionnels',
            ],
            [
                'name' => 'koffa2',
                'description' => 'Sac artisanal en osier décoré d’un appliqué papillon avec perles, élégant et pratique.',
                'price' => '39.99',
                'stock' => 15,
                'image' => 'koffa2.png',
                'categoryName' => 'Accessoires',
            ],
            [
                'name' => 'koffa',
                'description' => 'Grand sac en osier avec anses en cuir et décorations en bois, parfait pour le quotidien.',
                'price' => '49.99',
                'stock' => 12,
                'image' => 'koffa.png',
                'categoryName' => 'Accessoires',
            ],
        ];

        foreach ($products as $productData) {
            $existingProduct = $manager->getRepository(Product::class)->findOneBy(['name' => $productData['name']]);
            if (!$existingProduct) {
                $category = $categories[$productData['categoryName']];
                $product = new Product();
                $product->setName($productData['name']);
                $product->setDescription($productData['description']);
                $product->setPrice($productData['price']);
                $product->setStock($productData['stock']);
                $product->setImage($productData['image']);
                $product->setCategory($category);
                $manager->persist($product);
            }
        }

        $manager->flush();
    }
}