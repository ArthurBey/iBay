<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        /*
        $productId = $productRepo->find(5)->getId();
        $reviews = $repo->findBy(['product' => $productId]);
        $sum = 0;
        foreach($reviews as $key => $review){
            $sum += $review->getRating();
        }
        if(count($reviews) > 0) {
            $avg = $sum / count($reviews);
        }
        dd($avg); */

        return $this->render('index.html.twig');
    }
}
