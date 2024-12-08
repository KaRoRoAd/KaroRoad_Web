<?php

declare(strict_types=1);

namespace App\Task\Mapper;

use App\Shared\Owner\OwnerProviderInterface;
use App\Task\ApiResource\TaskResource;
use App\Task\Entity\Task;
use App\Task\Repository\TaskRepositoryInterface;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;

#[AsMapper(from: TaskResource::class, to: Task::class)]
final readonly class TaskApiResourceToEntityMapper implements MapperInterface
{
    public function __construct(
        private TaskRepositoryInterface $repository,
        private OwnerProviderInterface $ownerProvider
    ) {
    }

    public function load(object $from, string $toClass, array $context): object
    {
        $dto = $from;
        assert($dto instanceof TaskResource);

        return $dto->id ? $this->repository->find($dto->id) : new Task(
            name: $dto->name,
            deadLine: $dto->deadLine,
        );
    }

    public function populate(object $from, object $to, array $context): object
    {
        $dto = $from;
        $entity = $to;
        assert($dto instanceof TaskResource);
        assert($entity instanceof Task);

        $entity->setName($dto->name);
        $entity->setDeadLine($dto->deadLine);
        $entity->setOwnerId($this->ownerProvider->getOwnerDto()?->id);

        return $entity;
    }
}
