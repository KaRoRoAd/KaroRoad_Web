<?php

declare(strict_types=1);

namespace App\Task\ApiResource;

use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Shared\State\EntityClassDtoStateProcessor;
use App\Shared\State\EntityToDtoStateProvider;
use App\Task\Entity\Task;
use DateTime;

#[ApiResource(
    shortName: 'Task',
    operations: [
        new GetCollection(
            uriTemplate: '/task',
            stateless: true,
            description: 'Get all tasks',
            name: 'api_get_tasks'
        ),
        new Get(
            uriTemplate: '/task/{id}',
            requirements: ['id' => '\d+'],
            stateless: true,
            description: 'Get a task',
            name: 'api_get_task'
        ),
        new Post(
            uriTemplate: '/task',
            stateless: true,
            status: 202,
            description: 'Create a new task',
            output: false,
            name: 'api_create_task'
        ),
        new Patch(
            uriTemplate: '/task/{id}',
            stateless: true,
            description: 'Update a task',
            name: 'api_update_task'
        ),
        new Delete(
            uriTemplate: '/task/{id}',
            requirements: ['id' => '\d+'],
            stateless: true,
            description: 'Delete a task',
            name: 'api_delete_task'
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
        public ?DateTime $deadLine = null,
        public ?int $employeeId = null,
        public ?string $status = null,
    ) {
    }
}
