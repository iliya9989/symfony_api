<?php

namespace App\Repository;

use App\Entity\Nemesis;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Nemesis>
 *
 * @method Nemesis|null find($id, $lockMode = null, $lockVersion = null)
 * @method Nemesis|null findOneBy(array $criteria, array $orderBy = null)
 * @method Nemesis[]    findAll()
 * @method Nemesis[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NemesisRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Nemesis::class);
    }

//    /**
//     * @return Nemesis[] Returns an array of Nemesis objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('n.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Nemesis
//    {
//        return $this->createQueryBuilder('n')
//            ->andWhere('n.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
