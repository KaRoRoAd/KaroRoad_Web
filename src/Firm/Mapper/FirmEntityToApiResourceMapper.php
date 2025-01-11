<?php

declare(strict_types=1);

namespace App\Firm\Mapper;

use App\Firm\ApiResource\FirmResource;
use App\Firm\Entity\Firm;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;

#[AsMapper(from: Firm::class, to: FirmResource::class)]
final readonly class FirmEntityToApiResourceMapper implements MapperInterface
{
    public function load(object $from, string $toClass, array $context): object
    {
        $entity = $from;
        assert($entity instanceof Firm);

        $dto = new FirmResource();
        $dto->id = $entity->getId();

        return $dto;
    }

    public function populate(object $from, object $to, array $context): object
    {
        $entity = $from;
        $dto = $to;
        assert($entity instanceof Firm);
        assert($dto instanceof FirmResource);

        $dto->name = $entity->getName();

        return $dto;
    }
}
