<?php

declare(strict_types=1);

namespace App\Task\Dto;

use App\Task\Entity\Task;
use DateTime;

final readonly class TaskDto
{
    private function __construct(
        public ?int    $id = null,
        public ?string $name = null,
        public ?DateTime $deadLine = null
    ) {
    }

    public static function fromReference(int $id): self
    {
        return new self(id: $id);
    }

    public static function fromClient(Task $task): self
    {
        return new self(
            id: $task->getId(),
            name: $task->getName(),
            deadLine: $task->getName(),
        );
    }
}
