<?php

declare(strict_types=1);

namespace App\Firm\Repository;

use App\Firm\Entity\Firm;
use App\Task\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Task>
 */
final class FirmRepository extends ServiceEntityRepository implements FirmRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Firm::class);
    }

    public function save(Firm $firm): Firm
    {
        $em = $this->getEntityManager();
        $em->persist($firm);
        $em->flush();

        return $firm;
    }
}
