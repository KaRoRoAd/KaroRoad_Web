<?php

declare(strict_types=1);

namespace App\Tests\Security\Unit\Handler;

use App\Security\Handler\AddUserToFirmCommand;
use App\Security\Handler\AddUserToFirmHandler;
use App\Security\Repository\UserQueryRepositoryInterface;
use App\Security\Repository\UserRepositoryInterface;
use App\Security\Entity\User;
use PHPUnit\Framework\TestCase;

final class AddUserToFirmHandlerTest extends TestCase
{
    public function testCanAddUserToFirm(): void
    {
        $userQueryRepository = $this->createMock(UserQueryRepositoryInterface::class);
        $userRepository = $this->createMock(UserRepositoryInterface::class);

        $user = $this->createMock(User::class);
        $user->expects(self::once())
            ->method('setFirmId')
            ->with(123);

        $userQueryRepository->expects(self::once())
            ->method('findOneByEmail')
            ->with('user@example.com')
            ->willReturn($user);

        $userRepository->expects(self::once())
            ->method('save')
            ->with($user);

        $handler = new AddUserToFirmHandler(
            $userQueryRepository,
            $userRepository
        );

        $command = new AddUserToFirmCommand(
            email: 'user@example.com',
            firmId: 123
        );

        $handler($command);
    }
}
