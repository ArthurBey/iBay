<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ImageRepository;
use App\Service\Pagination;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
        $findBySettings = [
            "category" => $category,
            "available" => true 
        ];
        // RAPPEL : cf services.yaml
        // On indique juste l'entité et la page actuelle
        $pagination->setEntityClass(Product::class)
                   ->setRoute('products_index') // on indique la route des liens cliquables de pagination
                   ->setLimit(8) // 8 au lieu du default de 10
                   ->setPage($page)
                   ->setFindBySettings($findBySettings)
                   ->setTemplatePath("/product/pagination.html.twig");

        return $this->render('product/index.html.twig', [
            'pagination' => $pagination // On laisse twig extraire les infos
        ]);
    }

    /**
     * Affiche la page d'un produit en vente
     * slug et id parceque le slug seul induit que les titres annonces doivent etres uniques
     * 
     * @Route("/products/{category<\d+>}/show/{slug}/{id<\d+>}", name="product_show")
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
    
    /**
     * Créer et gère le formulaire de publication de produit
     * 
     * @Route("/product/publish/", name="product_publish")
     * @IsGranted("ROLE_USER")
     *
     * @return Response
     */
    public function publish(Request $request, EntityManagerInterface $manager) 
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            
            foreach($product->getImages() as $image){
                $image->setProduct($product);
                $manager->persist($image);
            }
            
            $product->setCreatedAt(new \DateTime())
                    ->setAvailable(true)
                    ->setUser($user);

            $manager->persist($product);
            $manager->flush();

            $this->addFlash(
                "success",
                "Votre annonce a bien été publiée !"
            );

            return $this->redirectToRoute('product_show', [
                'category' => $product->getCategory()->getId(), 
                'slug' => $product->getSlug()
            ]);
        }
        
        return $this->render('product/publish.html.twig', [
            'productForm' => $form->createView()
        ]);
    }

    /**
     * Créer et gère le formulaire d'édition du produit
     * 
     * @Route("/product/edit/{slug}/{id<\d+>}", name="product_edit")
     * @Security("is_granted('ROLE_USER') and user === product.getUser()")
     *
     * @return Response
     */
    public function edit(Product $product, Request $request, EntityManagerInterface $manager) 
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            
            foreach($product->getImages() as $image){
                $image->setProduct($product);
                $manager->persist($image);
            }
            
            $product->setCreatedAt(new \DateTime())
                    ->setAvailable(true)
                    ->setUser($user);

            $manager->persist($product);
            $manager->flush();

            $this->addFlash(
                "success",
                "Votre annonce a bien été éditée !"
            );

            return $this->redirectToRoute('product_show', [
                'category' => $product->getCategory()->getId(), 
                'slug' => $product->getSlug()
            ]);
        }
        
        return $this->render('product/publish.html.twig', [
            'productForm' => $form->createView()
        ]);
    }
}
