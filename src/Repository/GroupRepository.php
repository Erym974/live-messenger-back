<?php

namespace App\Repository;

use App\Entity\Group;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Group>
 *
 * @method Group|null find($id, $lockMode = null, $lockVersion = null)
 * @method Group|null findOneBy(array $criteria, array $orderBy = null)
 * @method Group[]    findAll()
 * @method Group[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Group::class);
    }

//    /**
//     * @return Group[] Returns an array of Group objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('g.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

   /**
    * @return Group[] Returns an array of Group objects
    */
   public function getGroupByAdmin($admin): array
   {
       return $this->createQueryBuilder('g')
           ->andWhere('g.administrator = :user')
           ->setParameter('user', $admin)
           ->getQuery()
           ->getResult()
       ;
   }

    /**
    * @return Group[] Returns an array of Group object
    */
    public function findLast24(): array
    {
 
         $date = new \DateTime();
         $date->modify('-24 hours');
 
         return $this->createQueryBuilder('g')
             ->andWhere('g.createdAt >= :date')
             ->setParameter('date', $date)
             ->orderBy('g.id', 'ASC')
             ->getQuery()
             ->getResult()
         ;
    }

//    public function findOneBySomeField($value): ?Group
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
   public function findPrivateGroup($user, $user2): ?Group
   {
        return $this->createQueryBuilder('g')
            ->innerJoin('g.members', 'm1')
            ->innerJoin('g.members', 'm2')
            ->andWhere('g.private = :private')
            ->andWhere('m1 = :user')
            ->andWhere('m2 = :user2')
            ->setParameter('private', true)
            ->setParameter('user', $user)
            ->setParameter('user2', $user2)
           ->getQuery()
            ->getOneOrNullResult()
       ;
   }
}
