<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;

class ProductController extends AbstractController
{
    #[Route('/', name: 'app_product_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show($id, ProductRepository $productRepository, LoggerInterface $logger): Response
    {
        $logger->debug("Tentative d'accès au produit ID: ".$id);

        $product = $productRepository->find($id);

        if (!$product) {
            $logger->error("Produit introuvable - ID: ".$id);
            throw $this->createNotFoundException('Le produit demandé n\'existe pas');
        }

        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/admin/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ProductRepository $productRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productRepository->save($product, true);
            return $this->redirectToRoute('app_product_index');
        }

        return $this->render('product/admin/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/edit/{id}', name: 'app_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productRepository->save($product, true);
            return $this->redirectToRoute('app_product_index');
        }

        return $this->render('product/admin/edit.html.twig', [
            'form' => $form->createView(),
            'product' => $product,
        ]);
    }

    #[Route('/search', name: 'app_product_search', methods: ['GET'])]
    public function search(Request $request, ProductRepository $productRepository): Response
    {
        $query = $request->query->get('q');
        $products = $query ? $productRepository->findBySearch($query) : [];
        return $this->render('product/search.html.twig', [
            'products' => $products,
            'query' => $query,
        ]);
    }
}