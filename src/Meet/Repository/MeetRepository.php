<?php

declare(strict_types=1);

namespace App\Meet\Repository;

use App\Meet\Entity\Meet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @extends ServiceEntityRepository<Meet>
 */
final class MeetRepository extends ServiceEntityRepository implements MeetRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Meet::class);
    }
}
