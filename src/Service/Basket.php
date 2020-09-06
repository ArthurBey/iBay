<?php

namespace App\Service;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Basket {

    protected $session;
    protected $productRepository;

    public function __construct(SessionInterface $session, ProductRepository $productRepository){
        $this->session = $session;
        $this->productRepository = $productRepository;
    }

    public function add(int $id) {
        // Alternativement sans SessionInterface et avec un objet Request : $session = $request->getSession();
        $basket = $this->session->get('panier', []); /* "Dans la session regarde si y'a une donnée qui s'appelle panier, pour récupérer ce panier"
        Deuxieme arg: Par défaut renvoi null si pas de 2eme arg. Ici: si rien trouvé, alors on met un place un panier vide (cad un tableau vide...) */
        
        $product = $this->productRepository->find($id); 
        $stock = $product->getStock(); // pour vérifier qu'on commande pas + que le stock dispo
        
        if(!empty($basket[$id])) { // Sans cette 1ere condition, cliquer une 2eme fois sur un article ne rajoute pas cet article en +
            $quantityInBasket = $basket[$id];
            if ($stock > $quantityInBasket){
                $basket[$id]++;
            } else {
                return true; // déclenche un addFlash dans le BasketController
            }
        } else {
            $basket[$id] = 1; // [ id => 1 ] par ex click sur un produit avec id 12 et 13 -> [ 12 => 1, 13 => 1]
        }

        $this->session->set('panier', $basket);
    }

    public function remove(int $id) {

        $basket = $this->session->get('panier', []);
        
        if(!empty($basket[$id])) {
            unset($basket[$id]);
        }
        $this->session->set('panier', $basket);
    }

    public function getFullCart() : array {
        $basket = $this->session->get('panier', []); // Si pas de panier trouvé, alors new panier vide [] (null si pas de 2eme arg)
        $basketWithData = [];

        foreach($basket as $id => $quantity) {
            $basketWithData[] = [
                'product' => $this->productRepository->find($id),
                'quantity' => $quantity
            ];
        }

        return $basketWithData;
    }

    public function getTotal() : float {
        $total = 0;

        foreach($this->getFullCart() as $item) {
            $totalItem = $item['product']->getPrice() * $item['quantity'] * 1.2 + $item['product']->getShippingCost();
            $total += $totalItem; 
        }

        return $total;
    }

    public function getVat() : float {
        $total = 0;
  
        foreach($this->getFullCart() as $item) {
            $totalItem = $item['product']->getPrice() * $item['quantity'] * 0.2;
            $total += $totalItem; 
        }

        return $total;
    }
}