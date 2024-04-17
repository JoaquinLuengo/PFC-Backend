<?php

namespace App\Repository;

use App\Entity\SistemaOperativo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<SistemaOperativo>
 *
 * @method SistemaOperativo|null find($id, $lockMode = null, $lockVersion = null)
 * @method SistemaOperativo|null findOneBy(array $criteria, array $orderBy = null)
 * @method SistemaOperativo[]    findAll()
 * @method SistemaOperativo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SistemaOperativoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SistemaOperativo::class);
    }

    public function save(SistemaOperativo $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(SistemaOperativo $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return SistemaOperativo[] Returns an array of SistemaOperativo objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?SistemaOperativo
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
