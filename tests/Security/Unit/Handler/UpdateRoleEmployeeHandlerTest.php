<?php

declare(strict_types=1);

namespace App\Tests\Security\Unit\Handler;

use App\Security\Handler\UpdateRoleEmployeeCommand;
use App\Security\Handler\UpdateRoleEmployeeHandler;
use App\Security\Repository\UserQueryRepositoryInterface;
use App\Security\Repository\UserRepositoryInterface;
use App\Security\Entity\User;
use PHPUnit\Framework\TestCase;

final class UpdateRoleEmployeeHandlerTest extends TestCase
{
    public function testCanUpdateEmployeeRoles(): void
    {
        $userQueryRepository = $this->createMock(UserQueryRepositoryInterface::class);
        $userRepository = $this->createMock(UserRepositoryInterface::class);

        $user = $this->createMock(User::class);
        $roles = ['ROLE_ADMIN', 'ROLE_USER'];

        $user->expects(self::once())
            ->method('setRoles')
            ->with($roles);

        $userQueryRepository->expects(self::once())
            ->method('findOneByEmail')
            ->with('employee@example.com')
            ->willReturn($user);

        $userRepository->expects(self::once())
            ->method('save')
            ->with($user);

        $handler = new UpdateRoleEmployeeHandler(
            $userQueryRepository,
            $userRepository
        );

        $command = new UpdateRoleEmployeeCommand(
            email: 'employee@example.com',
            roles: $roles
        );

        $handler($command);
    }
}
