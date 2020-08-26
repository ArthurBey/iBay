<?php

namespace App\Controller;

use App\Entity\Product;
use App\Service\Pagination;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    /**
     * @Route("/products/{category<\d+>?1}/{page<\d+>?1}", name="products_index")
     * \d+ signifie un ou plusieurs nombres & le ? signifie param optionnel, 1 val par défaut => inlined requirement
     * Sans le inline requirement : fonction est effectivement appelée (à tort). Alors que avec affiche cash msg erreur 
     */
    public function index($page, $category, Pagination $pagination) // page = 1 plus nécessaire grace à la inlined requirement en annotation "?1" 
    {
        $findByCategory = [
            "category" => $category
        ];
        // RAPPEL : cf services.yaml
        // On indique juste l'entité et la page actuelle
        $pagination->setEntityClass(Product::class)
                   ->setRoute('products_index') // on indique la route des liens cliquables de pagination
                   ->setLimit(8) // 8 au lieu du default de 10
                   ->setPage($page)
                   ->setFindByCategory($findByCategory);

        return $this->render('product/index.html.twig', [
            'pagination' => $pagination // On laisse twig extraire les infos
        ]);
    }

    /**
     * Affiche la page d'un produit en vente
     * 
     * @Route("/products/{category}/show/{slug}", name="product_show")
     *
     * @param string $slug
     * @return Response
     */
    public function show(Product $product, EntityManagerInterface $manager)
    {
        return $this->render('product/show.html.twig', [
            'product' => $product
        ]);
    }
}
