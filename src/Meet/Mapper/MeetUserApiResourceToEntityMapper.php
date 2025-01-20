<?php

declare(strict_types=1);

namespace App\Meet\Mapper;

use App\Meet\ApiResource\MeetUserResource;
use App\Meet\Entity\MeetsUsers;
use App\Meet\Repository\MeetsUsersRepositoryInterface;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;

#[AsMapper(from: MeetUserResource::class, to: MeetsUsers::class)]
final readonly class MeetUserApiResourceToEntityMapper implements MapperInterface
{
    public function __construct(private MeetsUsersRepositoryInterface $repository)
    {
    }

    public function load(object $from, string $toClass, array $context): object
    {
        $dto = $from;
        assert($dto instanceof MeetUserResource);

        return $dto->id ? $this->repository->find($dto->id) : new MeetsUsers();
    }

    public function populate(object $from, object $to, array $context): object
    {
        $dto = $from;
        $entity = $to;
        assert($dto instanceof MeetUserResource);
        assert($entity instanceof MeetsUsers);

        $entity->setMeetId($dto->meetId);
        $entity->setEmail($dto->email);

        return $entity;
    }
}
