<?php

namespace App\Controller;

use App\Service\Basket;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BasketController extends AbstractController
{
     /**
     * @Route("/panier", name="basket_index")
     */
    public function index(Basket $basket)
    {
        return $this->render('basket/index.html.twig', [
            'items' => $basket->getFullCart(),
            'total' => $basket->getTotal(),
            'vat' => $basket->getVat()
        ]);
    }

    /**
     * @Route("/panier/add/{id}", name="basket_add")
     */
    public function add($id, Basket $basket) {
        
        if($basket->add($id) === true){ // Le service basket retourne 'true' si stock indispo
            $this->addFlash("warning", "Vous avez atteint la limite d'achat pour ce produit");
        } else {
            $this->addFlash("success", "Produit ajouté au panier !");
        }

        return $this->redirectToRoute("basket_index");
    }

    /**
     * Deletes an item from the cart
     *
     * @Route("/panier/remove/{id}", name="basket_remove")
     * 
     * @param [type] $id
     * @param EntityManagerInterface $manager
     * @param SessionInterface $session
     * @return void
     */
    public function remove($id, Basket $basket) {

        $basket->remove($id);

        return $this->redirectToRoute("basket_index");
    }
}
