<?php

namespace App\Repository;

use App\Entity\Invitation;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Invitation>
 *
 * @method Invitation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Invitation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Invitation[]    findAll()
 * @method Invitation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InvitationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Invitation::class);
    }

    public function findInvitation(User $user, User $friend): ?Invitation
    {
        return $this->createQueryBuilder('i')
            ->where('(i.emitter = :user AND i.receiver = :friend) OR (i.emitter = :friend AND i.receiver = :user)')
            ->setParameter('user', $user)
            ->setParameter('friend', $friend)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    //    /**
    //     * @return Invitation[] Returns an array of Invitation objects
    //     */
    public function findInvitations(User $user): array
    {
        return $this->createQueryBuilder('i')
            ->where('i.emitter = :user OR i.receiver = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult()
        ;
    }

    //

//    /**
//     * @return Invitation[] Returns an array of Invitation objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('i.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Invitation
//    {
//        return $this->createQueryBuilder('i')
//            ->andWhere('i.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
