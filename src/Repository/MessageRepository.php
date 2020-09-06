<?php

namespace App\Repository;

use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function getSentThreads($user) 
    {
        $queryData = $this->getEntityManager()->createQuery( // IDENTITY  () pour SELECT un FK avec doctrine
            "SELECT m.id, IDENTITY(m.sender) as sender, IDENTITY(m.thread) as thread
             FROM App\Entity\Message m
             WHERE m.thread IS NULL
             AND m.sender = :id"
            )->setParameter('id', $user->getId())
             ->getResult();
        
        $sentThreadsArrays = [];
        foreach($queryData as $data){
            $sentThreadsArrays[] = $this->findBy([
                "id" => $data['id']
            ]);
        }
        // Les données sont dans un tableau multi-dimension, illisible pour twig...
        $sentThreads = []; 
        foreach ($sentThreadsArrays as $key1 => $value1) { 
            foreach($value1 as $key2 => $value2) {
                $sentThreads[$key1] = $value2;
            }
        } 

        return $sentThreads;
    }

    // /**
    //  * @return Message[] Returns an array of Message objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Message
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
