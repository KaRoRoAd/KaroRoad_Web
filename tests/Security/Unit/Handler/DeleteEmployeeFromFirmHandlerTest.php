<?php

declare(strict_types=1);

namespace App\Tests\Security\Unit\Handler;

use App\Security\Handler\DeleteEmployeeFromFirmCommand;
use App\Security\Handler\DeleteEmployeeFromFirmHandler;
use App\Security\Repository\UserQueryRepositoryInterface;
use App\Security\Repository\UserRepositoryInterface;
use App\Security\Entity\User;
use PHPUnit\Framework\TestCase;

final class DeleteEmployeeFromFirmHandlerTest extends TestCase
{
    public function testCanDeleteEmployeeFromFirm(): void
    {
        $userQueryRepository = $this->createMock(UserQueryRepositoryInterface::class);
        $userRepository = $this->createMock(UserRepositoryInterface::class);

        $user = $this->createMock(User::class);
        $user->expects(self::once())
            ->method('setFirmId')
            ->with(null);

        $userQueryRepository->expects(self::once())
            ->method('findOneByEmail')
            ->with('employee@example.com')
            ->willReturn($user);

        $userRepository->expects(self::once())
            ->method('save')
            ->with($user);

        $handler = new DeleteEmployeeFromFirmHandler(
            $userQueryRepository,
            $userRepository
        );

        $command = new DeleteEmployeeFromFirmCommand(
            email: 'employee@example.com'
        );

        $handler($command);
    }
}
