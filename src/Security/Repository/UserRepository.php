<?php

declare(strict_types=1);

namespace App\Security\Repository;

use App\Security\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
final class UserRepository extends ServiceEntityRepository implements UserRepositoryInterface, UserLoaderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function save(UserInterface $user): UserInterface
    {
        $em = $this->getEntityManager();
        $em->persist($user);
        $em->flush();

        return $user;
    }

    public function loadUserByIdentifier(string $identifier): ?UserInterface
    {
        return $this->createQueryBuilder('ue')
            ->andWhere('ue.email = :email')
            ->setParameter('email', $identifier)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
