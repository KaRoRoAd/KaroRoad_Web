<?php

declare(strict_types=1);

namespace App\Meet\ApiExtension;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGenerator;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Meet\Entity\Meet;
use App\Meet\Entity\MeetsUsers;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;

final readonly class CurrentMeetExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    public function __construct(
        private Security $security,
    ) {
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, ?Operation $operation = null, array $context = []): void
    {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, ?Operation $operation = null, array $context = []): void
    {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    private function addWhere(QueryBuilder $queryBuilder, string $resourceClass): void
    {
        if ($resourceClass !== Meet::class || null === $user = $this->security->getUser()) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryNameGenerator = new QueryNameGenerator();

        // Subquery to find meets where user is assigned
        $subQueryAlias = $queryNameGenerator->generateJoinAlias('meets_users');
        $subQuery = $queryBuilder->getEntityManager()->createQueryBuilder()
            ->select('mu.meetId')
            ->from(MeetsUsers::class, 'mu')
            ->where('mu.userId = :current_user');

        // Main query conditions
        $queryBuilder->andWhere(
            $queryBuilder->expr()->orX(
                sprintf('%s.ownerId = :current_user', $rootAlias),
                sprintf('%s.id IN (%s)', $rootAlias, $subQuery->getDQL())
            )
        )->setParameter('current_user', $user->getId());
    }
}
