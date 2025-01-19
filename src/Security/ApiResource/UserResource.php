<?php

declare(strict_types=1);

namespace App\Security\ApiResource;

use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\Security\Entity\User;
use App\Security\Handler\ChangeUserPasswordCommand;
use App\Security\Handler\RegisterUserCommand;
use App\Security\Handler\ResetUserPasswordCommand;

#[ApiResource(
    shortName: 'Security',
    operations: [
        new Post(
            uriTemplate: '/users/register',
            stateless: false,
            status: 202,
            description: 'Register user',
            input: RegisterUserCommand::class,
            output: false,
            messenger: true,
            name: 'api_register_user'
        ),
        new Post(
            uriTemplate: '/users/reset_password',
            stateless: false,
            status: 202,
            description: 'Reset password user',
            input: ResetUserPasswordCommand::class,
            output: false,
            messenger: true,
            name: 'api_reset_password_user'
        ),
        new Post(
            uriTemplate: '/users/change_password',
            stateless: false,
            status: 202,
            description: 'Change Password',
            input: ChangeUserPasswordCommand::class,
            output: false,
            messenger: true,
            name: 'api_change_password_user'
        ),
    ],
    stateOptions: new Options(entityClass: User::class),
)
]
final class UserResource
{
    #[ApiProperty(readable: true, writable: false, identifier: true)]
    public ?int $id = null;
    public ?string $email = null;

    public function __construct(?int $id = null, ?string $email = null)
    {
        $this->id = $id;
        $this->email = $email;
    }
}
