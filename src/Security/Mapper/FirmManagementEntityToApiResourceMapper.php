<?php

declare(strict_types=1);

namespace App\Security\Mapper;

use App\Security\ApiResource\FirmManagementResource;
use App\Security\ApiResource\UserResource;
use App\Security\Entity\User;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;

#[AsMapper(from: User::class, to: FirmManagementResource::class)]
final readonly class FirmManagementEntityToApiResourceMapper implements MapperInterface
{
    public function load(object $from, string $toClass, array $context): object
    {
        $entity = $from;
        assert($entity instanceof User);

        $dto = new FirmManagementResource();
        $dto->id = $entity->getId();

        return $dto;
    }

    public function populate(object $from, object $to, array $context): object
    {
        $entity = $from;
        $dto = $to;

        assert($entity instanceof User);
        assert($dto instanceof FirmManagementResource);

        $dto->email = $entity->getEmail();
        $dto->roles = $entity->getRoles();

        return $dto;
    }
}
