<?php

namespace App\Repository;

use App\Entity\EventType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EventType>
 *
 * @method EventType|null find($id, $lockMode = null, $lockVersion = null)
 * @method EventType|null findOneBy(array $criteria, array $orderBy = null)
 * @method EventType[]    findAll()
 * @method EventType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EventType::class);
    }

    public function save(EventType $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(EventType $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @param array $keyValPairs
     * @return array|null
     */
    public function findByFields(array $keyValPairs): ?array
    {
        $query =  $this->createQueryBuilder('e');
        foreach ($keyValPairs as $key => $value) {
            $query->andWhere("e.$key = :$key")->setParameter($key , $value);
        }
        return $query->getQuery()->getResult();
    }

    /**
     * @param array $name
     * @param int $status
     * @return array|null
     */
    public function getEventTypeWithStatus(array $name, int $status): ?array
    {
        $query =  $this->createQueryBuilder('e');
        if(!empty($name)) {
            $query->andWhere('e.name IN (:name)')
                ->setParameter('name', $name, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY);
        }
        $query->andWhere('e.status = :val')->setParameter('val' , $status);

        return $query->getQuery()->getResult();
    }

//    /**
//     * @return EventType[] Returns an array of EventType objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?EventType
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
