<?php

declare(strict_types=1);

namespace App\Firm\ApiResource;

use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Firm\Entity\Firm;
use App\Shared\State\EntityClassDtoStateProcessor;
use App\Shared\State\EntityToDtoStateProvider;

#[ApiResource(
    shortName: 'Firm',
    operations: [
        new GetCollection(
            uriTemplate: '/firm',
            stateless: true,
            description: 'Get all firms',
            name: 'api_get_firms'
        ),
        new Get(
            uriTemplate: '/firm/{id}',
            requirements: ['id' => '\d+'],
            stateless: true,
            description: 'Get a firm',
            name: 'api_get_firm'
        ),
        new Post(
            uriTemplate: '/firm',
            stateless: true,
            status: 202,
            description: 'Create a new firm',
            output: false,
            name: 'api_create_firm'
        ),
        new Patch(
            uriTemplate: '/firm/{id}',
            stateless: true,
            description: 'Update a firm',
            name: 'api_update_firm'
        ),
        new Delete(
            uriTemplate: '/firm/{id}',
            requirements: ['id' => '\d+'],
            stateless: true,
            description: 'Delete a firm',
            name: 'api_delete_firm'
        ),
    ],
    provider: EntityToDtoStateProvider::class,
    processor: EntityClassDtoStateProcessor::class,
    stateOptions: new Options(entityClass: Firm::class)
)
]
final class FirmResource
{
    public function __construct(
        #[ApiProperty(readable: true, writable: false, identifier: true)]
        public ?int $id = null,
        public ?string $name = null,
        public ?int $ownerId = null,
    ) {
    }
}
