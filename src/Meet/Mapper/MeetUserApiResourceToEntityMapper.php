<?php

declare(strict_types=1);

namespace App\Meet\Mapper;

use App\Meet\ApiResource\MeetResource;
use App\Meet\ApiResource\MeetUserResource;
use App\Meet\Entity\Meet;
use App\Meet\Entity\MeetsUsers;
use App\Meet\Repository\MeetRepositoryInterface;
use App\Shared\Owner\OwnerProviderInterface;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;

#[AsMapper(from: MeetUserResource::class, to: MeetsUsers::class)]
final readonly class MeetUserApiResourceToEntityMapper implements MapperInterface
{
    public function __construct(
        private MeetRepositoryInterface $repository,
        private OwnerProviderInterface $ownerProvider
    ) {
    }

    public function load(object $from, string $toClass, array $context): object
    {
        $dto = $from;
        assert($dto instanceof MeetResource);

        return $dto->id ? $this->repository->find($dto->id) : new Meet(
            name: $dto->name,
        );
    }

    public function populate(object $from, object $to, array $context): object
    {
        $dto = $from;
        $entity = $to;
        assert($dto instanceof MeetResource);
        assert($entity instanceof Meet);

        $entity->setStartDate($dto->startDate);
        $entity->setEndDate($dto->endDate);
        $entity->setOwnerId($this->ownerProvider->getOwnerDto()?->id);

        return $entity;
    }
}
