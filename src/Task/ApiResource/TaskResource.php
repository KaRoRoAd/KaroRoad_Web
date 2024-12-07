<?php

declare(strict_types=1);

namespace App\Task\ApiResource;

use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Shared\State\EntityClassDtoStateProcessor;
use App\Shared\State\EntityToDtoStateProvider;
use App\Task\Entity\Task;
use DateTime;

#[ApiResource(
    shortName: 'Task',
    operations: [
        new Post(
            uriTemplate: '/task',
            stateless: true,
            status: 202,
            description: 'Create a new task',
            output: false,
            name: 'api_create_task'
        ),
    ],
    provider: EntityToDtoStateProvider::class,
    processor: EntityClassDtoStateProcessor::class,
    stateOptions: new Options(entityClass: Task::class)
)
]
final class TaskResource
{
    public function __construct(
        #[ApiProperty(readable: true, writable: false, identifier: true)]
        public ?int $id = null,
        public ?string $name = null,
        public ?DateTime $deadLine = null
    ) {
    }
}
