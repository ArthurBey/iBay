<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\MessageRepository;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MessageController extends AbstractController
{
    /**
     * @Route("/inbox/", name="messages_inbox")
     * @IsGranted("ROLE_USER")
     */
    public function inbox(MessageRepository $messageRepo)
    {
        $receivedThreads = $messageRepo->findBy([
            "receiver" => $this->getUser(),
            "thread" => null
        ]);
        //$lastMessage = $messageRepo;

        return $this->render('message/inbox.html.twig', [
            'threads' => $receivedThreads
        ]);
    }

    /**
     * @Route("/outbox", name="messages_outbox")
     * @IsGranted("ROLE_USER")
     */
    public function outbox(MessageRepository $messageRepo) 
    {
        $user = $this->getUser();
        $sentThreads = $messageRepo->getSentThreads($user); // méthode custom 
        return $this->render('message/outbox.html.twig', [
            'threads' => $sentThreads
        ]);
    }

    /**
     * @Route("/ask", name="message_ask")
     * @IsGranted("ROLE_USER")
     */
    public function ask()
    {
        return $this->render('message/index.html.twig', [
            'controller_name' => 'MessageController',
        ]);
    }

    /**
     * @Route("/reply", name="message_reply")
     * @IsGranted("ROLE_USER")
     */
    public function reply()
    {
        return $this->render('message/index.html.twig', [
            'controller_name' => 'MessageController',
        ]);
    }
}
