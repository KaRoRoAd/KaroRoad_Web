<?php

declare(strict_types=1);

namespace App\Shared\State;

use ApiPlatform\Doctrine\Orm\Paginator;
use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use ApiPlatform\Doctrine\Orm\State\ItemProvider;
use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\Pagination\TraversablePaginator;
use ApiPlatform\State\ProviderInterface;
use ArrayIterator;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfonycasts\MicroMapper\MicroMapperInterface;

final readonly class EntityToDtoStateProvider implements ProviderInterface
{
    public function __construct(
        #[Autowire(service: CollectionProvider::class)]
        private ProviderInterface $collectionProvider,
        #[Autowire(service: ItemProvider::class)]
        private ProviderInterface $itemProvider,
        private MicroMapperInterface $microMapper,
        private RequestStack $requestStack
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $resourceClass = $operation->getClass();

        $operationContext = [
            'collection' => $operation instanceof CollectionOperationInterface,
            'item' => $operation instanceof Get,
        ];

        if ($operation instanceof CollectionOperationInterface) {
            $entities = $this->collectionProvider->provide($operation, $uriVariables, $context);

            $dtos = [];
            foreach ($entities as $entity) {
                $dtos[] = $this->mapEntityToDto($entity, $resourceClass, $operationContext);
            }

            if (! $entities instanceof Paginator) {
                return new ArrayIterator($dtos);
            }

            assert($entities instanceof Paginator);

            return new TraversablePaginator(
                new ArrayIterator($dtos),
                $entities->getCurrentPage(),
                $entities->getItemsPerPage(),
                $entities->getTotalItems()
            );
        }

        $entity = $this->itemProvider->provide($operation, $uriVariables, $context);

        if (! $entity) {
            return null;
        }

        return $this->mapEntityToDto($entity, $resourceClass, $operationContext);
    }

    private function mapEntityToDto(object $entity, string $resourceClass, array $context = []): object
    {
        $context['filters'] = $this->requestStack->getCurrentRequest()->query->all();

        return $this->microMapper->map($entity, $resourceClass, $context);
    }
}
