<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function listByUser($userId)
    {
        $queryBuilder = $this->createQueryBuilder('user');
        $queryBuilder->innerJoin('user.Items', 'items');
        $queryBuilder->where(
            $queryBuilder->expr()->eq('user.id', $userId)
        );

        $query = $queryBuilder->getQuery();

        return $query->getResult();
    }

    public function oneItemByUser($userId, $itemId)
    {
        $queryBuilder = $this->createQueryBuilder('user');
        $queryBuilder->innerJoin('user.Items', 'items');
        $queryBuilder->where(
            $queryBuilder->expr()->eq('user.id', $userId)
        );
        $queryBuilder->andWhere(
            $queryBuilder->expr()->eq('items.id', $itemId)
        );

        $query = $queryBuilder->getQuery();

        return $query->getResult();
    }
}
