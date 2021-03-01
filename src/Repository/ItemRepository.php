<?php

namespace App\Repository;

use App\Entity\Item;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Item|null find($id, $lockMode = null, $lockVersion = null)
 * @method Item|null findOneBy(array $criteria, array $orderBy = null)
 * @method Item[]    findAll()
 * @method Item[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Item::class);
    }

 
    public function listByTodolist($todolistId)
    {
        $queryBuilder = $this->createQueryBuilder('item');
        $queryBuilder->innerJoin('item.todolist', 'todolist');
        $queryBuilder->where(
            $queryBuilder->expr()->eq('todolist.id', $todolistId)
        );

        $query = $queryBuilder->getQuery();

        return $query->getResult();
    }

    public function oneItemByTodolist($todolistId, $itemId)
    {
        $queryBuilder = $this->createQueryBuilder('item');
        $queryBuilder->innerJoin('item.todolist', 'todolist');
        $queryBuilder->where(
            $queryBuilder->expr()->eq('todolist.id', $todolistId)
        );
        $queryBuilder->andWhere(
            $queryBuilder->expr()->eq('item.id', $itemId)
        );

        $query = $queryBuilder->getQuery();

        return $query->getResult();
    }


    /*
    public function findOneBySomeField($value): ?Item
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
