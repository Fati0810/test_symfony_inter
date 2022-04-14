<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Form\ProductFormType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminProductController extends AbstractController
{
   # afficher la liste des product avec /
    // /**
    //  * @Route("/admin/products", name="admin_product_list")
    //  */
    public function adminProductList(ProductRepository $productRepository)
    {
        $products = $productRepository->findAll();

        return $this->render("admin/product_list.html.twig", ['products' => $products]);
    }

    # afficher un product avec son id

    // /**
    //  * @Route("admin/product/{id}", name="admin_product_show")
    //  */
    public function adminProductShow($id, ProductRepository $productRepository)
    {
        $product = $productRepository->find($id);

        return $this->render("admin/product_show.html.twig", ['product' => $product]);
    }

    # créer/ajouter

    //  /**
    //  * @Route("admin/create/product", name="admin_product_create")
    //  */
    public function adminCreateProduct(
        EntityManagerInterface $entityManagerInterface,
        Request $request
    ) {
        $product = new Product();

        $productForm = $this->createForm(ProductFormType::class, $product);

        $productForm->handleRequest($request);

        if ($productForm->isSubmitted() && $productForm->isValid()) {
            $entityManagerInterface->persist($product);
            $entityManagerInterface->flush();

            return $this->redirectToRoute('admin_product_list');
        }

        return $this->render("admin/product_form.html.twig", ['productForm' => $productForm->createView()]);
    }

    # modifier
    // /**
    //  * @Route("/{id}/update", name="admin_product_update")
    //  */
    public function adminProductUpdate(
        $id,
        ProductRepository $productRepository,
        EntityManagerInterface $entityManagerInterface,
        Request $request
    ) {

        $product = $productRepository->find($id);

        $productForm = $this->createForm(ProductFormType::class, $product);

        $productForm->handleRequest($request);

        if ($productForm->isSubmitted() && $productForm->isValid()) {
            $entityManagerInterface->persist($product);
            $entityManagerInterface->flush();

            return $this->redirectToRoute('admin_product_list');
        }

        return $this->render("admin/product_form.html.twig", ['productForm' => $productForm->createView()]);
    }

    
    # supprimer
    // /**
    //  * @Route("/delete/product/{id}", name="admin_product_delete")
    //  */
    public function adminDeleteProduct(
        $id,
        EntityManagerInterface $entityManagerInterface,
        ProductRepository $productRepository
    ) {
        $product = $productRepository->find($id);

        $entityManagerInterface->remove($product);
        $entityManagerInterface->flush();

        return $this->redirectToRoute('admin_product_list');
    }
} 

