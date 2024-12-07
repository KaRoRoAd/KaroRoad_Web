<?php

declare(strict_types=1);

namespace App\Task\Repository;

use App\Security\Entity\User;
use App\Task\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
final class TaskRepository extends ServiceEntityRepository implements TaskRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function save(Task $task): Task
    {
        $em = $this->getEntityManager();
        $em->persist($task);
        $em->flush();

        return $task;
    }
}
