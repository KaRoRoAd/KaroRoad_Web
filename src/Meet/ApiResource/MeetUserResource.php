<?php

declare(strict_types=1);

namespace App\Meet\ApiResource;

use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Post;
use App\Meet\Entity\MeetsUsers;
use App\Shared\State\EntityClassDtoStateProcessor;
use App\Shared\State\EntityToDtoStateProvider;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    shortName: 'MeetsUsers',
    operations: [
        new Post(
            uriTemplate: '/meet_user',
            stateless: true,
            status: 202,
            description: 'Create a new meetUser',
            output: false,
            name: 'api_create_meet_user'
        ),
        new Delete(
            uriTemplate: '/meet_user/{id}',
            requirements: ['id' => '\d+'],
            stateless: true,
            status: 204,
            description: 'Delete a meetUser',
            output: false,
            name: 'api_delete_meet_user'
        ),
    ],
    provider: EntityToDtoStateProvider::class,
    processor: EntityClassDtoStateProcessor::class,
    stateOptions: new Options(entityClass: MeetsUsers::class)
)
]
final class MeetUserResource
{
    public function __construct(
        #[ApiProperty(readable: true, writable: false, identifier: true)]
        public ?int $id = null,
        public ?int $meetId = null,
        #[Assert\NotBlank]
        #[Assert\Email]
        public ?string $email = null,
    ) {
    }
}
