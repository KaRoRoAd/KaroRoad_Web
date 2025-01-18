<?php

declare(strict_types=1);

namespace App\Security\ApiResource;

use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Security\Entity\User;
use App\Security\Handler\AddUserToFirmCommand;
use App\Security\Handler\DeleteEmployeeFromFirmCommand;
use App\Security\Handler\UpdateRoleEmployeeCommand;
use App\Shared\State\EntityToDtoStateProvider;

#[ApiResource(
    shortName: 'Security',
    operations: [
        new GetCollection(
            uriTemplate: '/firm_management',
            status: 200,
            description: 'Get all firm employees',
            output: FirmManagementResource::class,
            name: 'api_firm_management_get_all_employees',
        ),
        new Get(
            uriTemplate: '/firm_management/{id}',
            requirements: ['id' => '\d+'],
            status: 200,
            description: 'Get employee by id',
            output: FirmManagementResource::class,
            name: 'api_firm_management_get_employee_by_id',
        ),
        new Post(
            uriTemplate: '/firm_management/add_employee',
            stateless: false,
            status: 202,
            description: 'Add employee to firm',
            input: AddUserToFirmCommand::class,
            output: false,
            messenger: true,
            name: 'api_firm_management_add_employee',
        ),
        new Post(
            uriTemplate: '/firm_management/delete_employee',
            stateless: false,
            status: 202,
            description: 'Delete employee',
            input: DeleteEmployeeFromFirmCommand::class,
            output: false,
            messenger: true,
            name: 'api_firm_management_delete_employee',
        ),
        new Post(
            uriTemplate: '/firm_management/update_role_employee',
            stateless: false,
            status: 202,
            description: 'Update employee',
            input: UpdateRoleEmployeeCommand::class,
            output: false,
            messenger: true,
            name: 'api_firm_management_update_role_employee',
        ),
    ],
    provider: EntityToDtoStateProvider::class,
    stateOptions: new Options(entityClass: User::class),
)
]
final class FirmManagementResource
{
    public function __construct(
        #[ApiProperty(readable: true, writable: false, identifier: true)]
        public ?int $id = null,
        public ?string $email = null,
        public ?array $roles = null,
    ) {
    }
}
