<?php

declare(strict_types=1);

namespace App\Meet\Mapper;

use App\Meet\ApiResource\MeetResource;
use App\Meet\ApiResource\MeetUserResource;
use App\Meet\Entity\Meet;
use App\Meet\Entity\MeetsUsers;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;

#[AsMapper(from: MeetsUsers::class, to: MeetUserResource::class)]
final readonly class MeetUserEntityToApiResourceMapper implements MapperInterface
{
    public function load(object $from, string $toClass, array $context): object
    {
        $entity = $from;
        assert($entity instanceof Meet);

        $dto = new MeetResource();
        $dto->id = $entity->getId();

        return $dto;
    }

    public function populate(object $from, object $to, array $context): object
    {
        $entity = $from;
        $dto = $to;
        assert($entity instanceof Meet);
        assert($dto instanceof MeetResource);

       $dto->name = $entity->getName();
       $dto->startDate = $entity->getStartDate();
       $dto->endDate = $entity->getEndDate();

        return $dto;
    }
}
