<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function listByTodolist($todolistId)
    {
        $queryBuilder = $this->createQueryBuilder('category');
        $queryBuilder->innerJoin('category.todolists', 'todolist');
        $queryBuilder->where(
            $queryBuilder->expr()->eq('todolist.id', $todolistId)
        );

        $query = $queryBuilder->getQuery();

        return $query->getResult();
    }
}
