<?php

namespace App\Controller;

use Faker\Factory;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        $faker = Factory::create('FR-fr');

        $array = ["a", "b", "c"];
        $letter = $array[mt_rand(0, count($array))];
        dd($letter);

        return $this->render('index.html.twig');
    }
}
