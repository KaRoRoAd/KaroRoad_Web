<?php

declare(strict_types=1);

namespace App\Firm\Mapper;

use App\Shared\Owner\OwnerProviderInterface;
use App\Firm\ApiResource\FirmResource;
use App\Firm\Entity\Firm;
use App\Firm\Repository\FirmRepositoryInterface;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;

#[AsMapper(from: FirmResource::class, to: Firm::class)]
final readonly class FirmApiResourceToEntityMapper implements MapperInterface
{
    public function __construct(
        private FirmRepositoryInterface $repository,
        private OwnerProviderInterface $ownerProvider
    ) {
    }

    public function load(object $from, string $toClass, array $context): object
    {
        $dto = $from;
        assert($dto instanceof FirmResource);

        return $dto->id ? $this->repository->find($dto->id) : new Firm(
            name: $dto->name,
        );
    }

    public function populate(object $from, object $to, array $context): object
    {
        $dto = $from;
        $entity = $to;
        assert($dto instanceof FirmResource);
        assert($entity instanceof Firm);

        $entity->setName($dto->name);

        return $entity;
    }
}

