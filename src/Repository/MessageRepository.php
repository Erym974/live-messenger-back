<?php

namespace App\Repository;

use App\Entity\Group;
use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Message>
 *
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
    * @return Message[] Returns an array of Message objects
    */
    public function findMessagesOfGroup(Group $group, int $limit = 20, int $page = 1): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.conversation = :conversation')
            ->setParameter('conversation', $group)
            ->orderBy('m.id', 'DESC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
    * @return Message[] Returns an array of Message objects
    */
   public function findLast24(): array
   {

        $date = new \DateTime();
        $date->modify('-24 hours');

        return $this->createQueryBuilder('m')
            ->andWhere('m.sended_at >= :date')
            ->setParameter('date', $date)
            ->orderBy('m.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
   }
//    /**
//     * @return Message[] Returns an array of Message objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Message
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
