<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Product;
use App\Service\Pagination;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AccountController extends AbstractController
{
    /**
     * @Route("/login", name="account_login")
     * @IsGranted("IS_ANONYMOUS")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('account/login.html.twig', [
            'last_username' => $lastUsername, 
            'error' => $error !== null
            ]);
    }

    /**
     * @Route("/logout", name="account_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * Permet d'afficher les détails publiques du compte d'un utilisateur
     * 
     * @Route("/account/{page<\d+>?1}/{slug}/{id}", name="account_public")
     *
     * @param User $user
     * @return Response
     */
    public function show(User $user, ProductRepository $productRepo, $id, $slug, Pagination $pagination, $page)
    {
        $findBySettings = [
            "user" => $id,
            "available" => true 
        ];
        // RAPPEL : cf services.yaml
        // On indique juste l'entité et la page actuelle

        $pagination->setEntityClass(Product::class)
                   ->setRoute('account_public') // on indique la route des liens cliquables de pagination
                   ->setLimit(8) // 8 au lieu du default de 10
                   ->setPage($page)
                   ->setFindBySettings($findBySettings)
                   ->setTemplatePath("/account/pagination.html.twig")
                   ->setId($id)
                   ->setSlug($slug);

        return $this->render('account/show.html.twig', [
            'user' => $user,
            'userRating' => $user->getAverageRating($productRepo->findBy(['user' => $id])),
            'pagination' => $pagination
        ]);
    }

}
