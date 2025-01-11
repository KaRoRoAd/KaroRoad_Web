<?php

declare(strict_types=1);

namespace App\Meet\ApiResource;

use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Meet\Entity\Meet;
use App\Shared\State\EntityClassDtoStateProcessor;
use App\Shared\State\EntityToDtoStateProvider;

#[ApiResource(
    shortName: 'Meet',
    operations: [
        new GetCollection(
            uriTemplate: '/meet',
            stateless: true,
            description: 'Get all meets',
            name: 'api_get_meets'
        ),
        new Get(
            uriTemplate: '/meet/{id}',
            requirements: ['id' => '\d+'],
            stateless: true,
            description: 'Get a meet',
            name: 'api_get_meet'
        ),
        new Post(
            uriTemplate: '/meet',
            stateless: true,
            status: 202,
            description: 'Create a new meet',
            output: false,
            name: 'api_create_meet'
        ),
        new Patch(
            uriTemplate: '/meet/{id}',
            stateless: true,
            description: 'Update a meet',
            name: 'api_update_meet'
        ),
        new Delete(
            uriTemplate: '/meet/{id}',
            requirements: ['id' => '\d+'],
            stateless: true,
            description: 'Delete a meet',
            name: 'api_delete_meet'
        )
    ],
    provider: EntityToDtoStateProvider::class,
    processor: EntityClassDtoStateProcessor::class,
    stateOptions: new Options(entityClass: Meet::class)
)
]
final class MeetResource
{
    public function __construct(
        #[ApiProperty(readable: true, writable: false, identifier: true)]
        public ?int $id = null,
        public ?string $name = null,
        public ?int $ownerId = null,
        public ?\DateTimeInterface $startDate = null,
        public ?\DateTimeInterface $endDate = null
    ) {
    }
}
