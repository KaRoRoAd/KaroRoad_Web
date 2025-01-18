<?php

declare(strict_types=1);

namespace App\Security\Repository;

use App\Security\Entity\CodeForResetPassword;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class CodeQueryRepository extends ServiceEntityRepository implements CodeQueryRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CodeForResetPassword::class);
    }
}
