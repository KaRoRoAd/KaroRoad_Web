<?php

declare(strict_types=1);

namespace App\Meet\Repository;

use App\Meet\Entity\MeetsUsers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @extends ServiceEntityRepository<MeetsUsers>
 */
final class MeetsUsersRepository extends ServiceEntityRepository implements MeetsUsersRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MeetsUsers::class);
    }
}
