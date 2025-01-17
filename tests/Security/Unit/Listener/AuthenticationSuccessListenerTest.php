<?php

declare(strict_types=1);

namespace App\Tests\Security\Unit\Listener;

use App\Security\Entity\User;
use App\Security\Listener\AuthenticationSuccessListener;
use App\Security\Repository\FirmQueryRepositoryInterface;
use DateTime;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\UserInterface;

final class AuthenticationSuccessListenerTest extends TestCase
{
    public function testOnJwtCreated(): void
    {
        $firmQueryRepository = $this->createMock(FirmQueryRepositoryInterface::class);
        $firmQueryRepository->expects($this->once())
            ->method('getFirmUuid')
            ->with($this->equalTo(123))
            ->willReturn('firm-uuid-123');

        $listener = new AuthenticationSuccessListener($firmQueryRepository);

        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn(1);
        $user->method('getRoles')->willReturn(['ROLE_USER']);
        $user->method('getFirmId')->willReturn(123);
        $user->method('getExpiresAt')->willReturn(new DateTime('2024-01-01'));
        $user->method('isLocked')->willReturn(false);
        $user->method('isActive')->willReturn(true);
        $user->method('isEnabled')->willReturn(true);

        $event = $this->createMock(JWTCreatedEvent::class);
        $event->expects($this->once())
            ->method('getUser')
            ->willReturn($user);

        $event->expects($this->once())
            ->method('getData')
            ->willReturn(['sub' => 'user-subject']);

        $event->expects($this->once())
            ->method('setData')
            ->with($this->callback(static function (array $payload) {
                return $payload['id'] === 1 &&
                    $payload['roles'] === ['ROLE_USER'] &&
                    $payload['firmUuid'] === 'firm-uuid-123' &&
                    $payload['firmId'] === 123 &&
                    $payload['expiresAt'] instanceof DateTime;
            }));

        $listener->onJwtCreated($event);
    }

    public function testOnJwtCreatedThrowsExceptionForLockedUser(): void
    {
        $firmQueryRepository = $this->createMock(FirmQueryRepositoryInterface::class);
        $listener = new AuthenticationSuccessListener($firmQueryRepository);

        $user = $this->createMock(User::class);
        $user->method('isLocked')->willReturn(true);  // User is locked
        $user->method('isActive')->willReturn(true);  // User is active
        $user->method('isEnabled')->willReturn(true);

        $event = $this->createMock(JWTCreatedEvent::class);
        $event->method('getUser')->willReturn($user);

        $this->expectException(Exception::class);
        $listener->onJwtCreated($event);
    }

    public function testOnJwtCreatedThrowsExceptionForInactiveUser(): void
    {
        $firmQueryRepository = $this->createMock(FirmQueryRepositoryInterface::class);
        $listener = new AuthenticationSuccessListener($firmQueryRepository);

        $user = $this->createMock(User::class);
        $user->method('isLocked')->willReturn(false);  // User is not locked
        $user->method('isActive')->willReturn(false);  // User is inactive
        $user->method('isEnabled')->willReturn(true);

        $event = $this->createMock(JWTCreatedEvent::class);
        $event->method('getUser')->willReturn($user);

        $this->expectException(Exception::class);
        $listener->onJwtCreated($event);
    }

    public function testOnJwtCreatedSkipsIfUserIsNotInstanceOfUser(): void
    {
        $firmQueryRepository = $this->createMock(FirmQueryRepositoryInterface::class);
        $listener = new AuthenticationSuccessListener($firmQueryRepository);

        $mockNonUser = $this->createMock(UserInterface::class);

        // Mock eventu
        $event = $this->createMock(JWTCreatedEvent::class);
        $event->expects($this->once())
            ->method('getUser')
            ->willReturn($mockNonUser);

        $event->expects($this->never())
            ->method('setData');

        $listener->onJwtCreated($event);
    }
}
