<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\Image;
use App\Entity\Order;
use App\Entity\Review;
use App\Entity\Status;
use App\Entity\Message;
use App\Entity\Product;
use App\Entity\Category;
use App\Entity\Condition;
use App\Entity\OrderLine;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder) {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('FR-fr');
        
        // Création de l'administrateur : Role admin puis compte admin
        $roleAdmin = new Role();
        $roleAdmin->setTitle("ROLE_ADMIN");
        $manager->persist($roleAdmin);

        $adminAccount    = new User();

        $paragraphsArray =   $faker->paragraphs(3);
        $paragraphs      = implode(' ', $paragraphsArray);

        $adminAccount->addAccountRole($roleAdmin) // pour les propriétés qui stockent PLUSIEURS entités : 'add' et non pas 'set'
                     ->setFirstName('Arthur')
                     ->setLastName('Bachirov')
                     ->setEmail('arthur.bachirov@gmail.com')
                     // ATTENTION : Pour que le encodePassword() fonctionne pour l'utilisateur passé en 1er arg. Il faut que l'ENCODER de cet User soit définis dans security.yaml !
                     ->setPassword($this->passwordEncoder->encodePassword($adminAccount, 'AdminPassword123')) // le 1er arg : User : dans security.yaml son encoder y est defini
                     ->setDescription($paragraphs)
                     ->setCreatedAt(new \DateTime())
                     ->setNickname('Arthur9800');

        $manager->persist($adminAccount);

        // Catégories d'articles
        $categoryNames = ['Vetements', 'Automobile', 'Smartphones', 'Informatique', 'Meubles', 'Electromenagers', 'Bricolage', 'Jeux-videos', 'Consoles', 'Sport'];
        $categoryEnglishNames = ['clothing', 'car', 'cellphone', 'computers', 'furniture', 'appliances', 'tools', 'games', 'ps4', 'olympic'];
        $categories = [];
        foreach($categoryNames as $categoryName) {
            $category = new Category();
            $category->setTitle($categoryName);
            $manager->persist($category);
            $categories[] = $category;
            // slug généré par prePersist
        }

        // Création des status de commande
        $statusNames = ['En attente de paiement', 'En cours de préparation', 'Expédié']; // Attention si modification des names, modif un if dans order
        $statusArray = [];
        foreach($statusNames as $statusName) {
            $status = new Status();
            $status->setTitle($statusName);
            
            $manager->persist($status);
            $statusArray[] = $status;
        }

        $conditionNames = ['Occasion', 'Reconditioné', 'Neuf'];
        $conditions = [];
        foreach($conditionNames as $conditionName) {
            $condition = new Condition();
            $condition->setState($conditionName);

            $manager->persist($condition);
            $conditions[] = $condition;
        }

        // Création d'utilisateurs
        $users = [];
        $genres = ['male', 'female'];
        $orders = [];
        $products = [];
        for($i = 1; $i <= 15; $i++) {
            $user = new User();

            $createdAt       = $faker->dateTimeBetween('- 12 months');
            $username        = $faker->userName();
            $paragraphsArray = $faker->paragraphs(3);
            $paragraphs      = implode(' ', $paragraphsArray);
            $genre           = $faker->randomElement($genres);
            $picture         = "https://randomuser.me/api/portraits/";
            $pictureId       = $faker->numberBetween(1, 99) . ".jpg";
            $picture        .= ($genre == 'male' ? 'men/' : 'women/') . $pictureId;

            $hash      = $this->passwordEncoder->encodePassword($user, 'UserPassword123'); // ATTENTION : cf attention d'en haut

            $user->setFirstName($faker->firstname($genre)) // Faker peut génerer nom en f. du genre
                 ->setLastName($faker->lastname)
                 ->setEmail($faker->email)
                 ->setDescription($paragraphs)
                 ->setPassword($hash)
                 ->setProfilePicture($picture)
                 ->setCreatedAt($createdAt)
                 ->setNickname($username);
                 // Le role sera par défaut [] en bdd mais tout le monde ROLE_USER cf: User.php

            $manager->persist($user);
            $users[] = $user;

            // Les produits/annonces
            for($j = 1; $j <= mt_rand(6, 36); $j++) {
                $product = new Product();

                $months           = $createdAt->diff(new \DateTime())->format("%m");
                $days             = $createdAt->diff(new \DateTime())->format("%d");
                $createdAt        = $faker->dateTimeBetween("- $months months - $days days");
                $paragraphsArray  = $faker->paragraphs(3);
                $paragraphs       = implode(' ', $paragraphsArray);
                $category         = $categories[mt_rand(0, count($categories) -1)]; 
                $categoryLink     = $category->getSlug();
                $words            = $faker->words(mt_rand(1, 2));
                $name             = implode(' ', $words);
                $productCondition = $conditions[mt_rand(0, count($conditions) -1)];

                foreach($categoryNames as $categoryName){ // Ici il s'agit de faire correspondre nom categ en FR au nom categ en anglais
                    if($categoryLink == strtolower($categoryName)){ // strtolower pour faire correspondre au slug
                        foreach($categoryEnglishNames as $categoryEnglishName) {
                            if(array_search($categoryEnglishName, $categoryEnglishNames) == array_search($categoryName, $categoryNames)) {
                                $categoryLink = $categoryEnglishName;
                            }
                        }
                    }
                }
                

                $product->setCreatedAt($createdAt)
                        ->setUser($user)
                        ->setCategory($category)
                        ->setTitle($faker->sentence())
                        ->setPrice(mt_rand(1, 499))
                        ->setShippingCost(mt_rand(0, 40))
                        ->setStock(mt_rand(1, 15))
                        ->setDetails($paragraphs)
                        ->setAvailable(true)
                        ->setCoverUrl("https://loremflickr.com/320/240/$categoryLink")
                        ->setName($name)
                        ->setProductCondition($productCondition);
                
                $manager->persist($product);
                $products[] = $product;

                // Les images des produits
                for($k = 1; $k <= mt_rand(2, 3); $k++) {
                    $image = new Image();
                    $image->setProduct($product)
                          ->setLink("https://loremflickr.com/320/240/$categoryLink")
                          ->setDescription($faker->sentence(5));

                    $manager->persist($image);
                }

                // Les commentaires sur les produits
                if($i > 3) { // Pour qu'il y ai un minimum de 3 utilisateurs générés. Donc tout les produits issu des 3 premières itérations d'user seront vides...
                   for($k = 1; $k <= mt_rand(0, 5); $k++) {
                    $review          = new Review();

                    $months          = $createdAt->diff(new \DateTime())->format("%m");
                    $days            = $createdAt->diff(new \DateTime())->format("%d");
                    $createdAt       = $faker->dateTimeBetween("- $months months - $days days");
                    $paragraphsArray = $faker->paragraphs(mt_rand(1, 4));
                    $paragraphs      = implode(' ', $paragraphsArray);
                    $userDraw        = $users[mt_rand(0, count($users) -1)];
                    
                    while(true) {
                        if ($userDraw == $user) {
                            $userDraw = $users[mt_rand(0, count($users) -1)]; // Pour que créateur de l'annonce commente pas sur sa propre annonce
                        } else {
                            break;
                        }
                    }

                    $review->setUser($userDraw)
                           ->setProduct($product)
                           ->setRating(mt_rand(1, 5))
                           ->setComment($paragraphs)
                           ->setCreatedAt($createdAt);
                    
                    $manager->persist($review);
                    } 

                    // Les orders
                    if(mt_rand(0, 1)){ // Tous les produits n'auront pas de "order". NB: pas obligé de faire dans la boucle $i
                        for($k = 1; $k <= mt_rand(1, 4); $k++) {

                            $order     = new Order();
                            $months    = $createdAt->diff(new \DateTime())->format("%m"); //$createdAt du produit
                            $days      = $createdAt->diff(new \DateTime())->format("%d");
                            $createdAt = $faker->dateTimeBetween("- $months months - $days days");
                            $userDraw  = $users[mt_rand(0, count($users) -1)];
                            $status    = $statusArray[mt_rand(0, count($statusArray) -1)];

                            if($status->getTitle() == 'En attente de paiement') {
                                $paid = false;
                            } else {
                                $paid = true;
                            }
                            
                            while(true) {
                                if ($userDraw == $user) {
                                    $userDraw = $users[mt_rand(0, count($users) -1)]; // Pour que vendeur du produit 'achete' pas son propre produit
                                } else {
                                    break;
                                }
                            }

                            $order->setStatus($status)
                                  ->setUser($userDraw)
                                  ->setShippingAddress($faker->streetAddress() . ', ' . $faker->postcode() . ', ' . $faker->city() )
                                  ->setPaid($paid)
                                  ->setVat(20)
                                  ->setCreatedAt($createdAt);
                            
                            $manager->persist($order);
                            $orders[] = $order;
                        }
                    }
                }   
            }
        }
        
        // Les OrderLines
        foreach($orders as $order) {
            for($i = 1; $i <= mt_rand(1, 6); $i++) {
                $orderLine   = new OrderLine();
                $productDraw = $products[mt_rand(0, count($products) -1)];
                $price = $productDraw->getPrice();
                $orderLine->setProduct($productDraw)
                          ->setProductOrder($order) // nom de propriété Order impossible à cause de sql donc 'productOrder'
                          ->setQuantity(mt_rand(1, 6))
                          ->setPrice($price); // Prix unitaire 

                $manager->persist($orderLine);
            }
        }

        // Création de fils de discussions entre utilisateurs 
        $threads = [];
        for($i = 1; $i <= 25; $i++) {
            $thread   = new Message();

            $sender   = $users[mt_rand(0, count($users) -1)]; // -1 car 1er user sur index 0
            $receiver = $users[mt_rand(0, count($users) -1)];
    
            while(true) {
                if($sender == $receiver) {
                    $receiver = $users[mt_rand(0, count($users) -1)];
                } else {
                    break;
                }
            }
            
            $paragraphsArray =  $faker->paragraphs(3);
            $paragraphs      = implode(' ', $paragraphsArray);
            $createdAt       = $faker->dateTimeBetween('- 2 months');

            $thread->setTitle(($faker->sentence(8) . ' ?'))
                   ->setContent($paragraphs)
                   ->setCreatedAt($createdAt)
                   ->setSender($sender)
                   ->setReceiver($receiver);
        
            $manager->persist($thread);
            $threads[] = $thread;

            // Création de quelques messages dans un fil de discussion
            for($j = 1; $j <= mt_rand(2, 7); $j++) {
                $message         = new Message();

                $paragraphsArray =  $faker->paragraphs(3);
                $paragraphs      = implode(' ', $paragraphsArray);
                $months          = $createdAt->diff(new \DateTime())->format('%m'); // createdAt du thread
                $days            = $createdAt->diff(new \DateTime())->format('%d');     
                $createdAt       = $faker->dateTimeBetween("- $months months - $days days"); // ainsi date du message postérieur à célui du thread
                
                $draw            = [$sender, $receiver];
                $draw1           = $draw[array_rand($draw)];
                $draw2           = $draw[array_rand($draw)];
                while(true) { // Pour éviter que le sender = receiver
                    if($draw1 == $draw2) {
                        $draw2 = $draw[array_rand($draw)];
                    } else {
                        break;
                    }
                }

                $message->setContent($paragraphs)
                        ->setSender($draw1)
                        ->setReceiver($draw2)
                        ->setCreatedAt($createdAt)
                        ->setThread($thread); // Le thread auquel appartient le message
                
                $manager->persist($message);
            }
        }

        $manager->flush();
    }                

}
