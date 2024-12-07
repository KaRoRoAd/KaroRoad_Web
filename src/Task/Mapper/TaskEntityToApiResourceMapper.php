<?php

declare(strict_types=1);

namespace App\Task\Mapper;

use App\Task\ApiResource\TaskResource;
use App\Task\Entity\Task;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;

#[AsMapper(from: Task::class, to: TaskResource::class)]
final class TaskEntityToApiResourceMapper implements MapperInterface
{
    public function load(object $from, string $toClass, array $context): object
    {
        $entity = $from;
        assert($entity instanceof Task);

        $dto = new TaskResource();
        $dto->id = $entity->getId();

        return $dto;
    }

    public function populate(object $from, object $to, array $context): object
    {
        $entity = $from;
        $dto = $to;
        assert($entity instanceof Task);
        assert($dto instanceof TaskResource);

       $dto->name = $entity->getName();
       $dto->deadLine = $entity->getDeadLine();

        return $dto;
    }
}
