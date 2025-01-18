<?php

declare(strict_types=1);

namespace App\Security\Repository;

use App\Security\Entity\CodeForResetPassword;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CodeForResetPassword>
 */
final class CodeRepository extends ServiceEntityRepository implements CodeRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CodeForResetPassword::class);
    }

    public function save(CodeForResetPassword $code): ?CodeForResetPassword
    {
        $em = $this->getEntityManager();
        $em->persist($code);
        $em->flush();

        return $code;
    }
}
