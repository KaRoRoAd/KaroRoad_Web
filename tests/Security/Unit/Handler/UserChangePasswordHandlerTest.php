<?php

declare(strict_types=1);

namespace App\Tests\Security\Unit\Handler;

use App\Security\EmailTokenGenerator\EmailTokenGeneratorInterface;
use App\Security\EmailTokenGenerator\ExceptionCommunicationEnum;
use App\Security\Handler\ChangePasswordCommand;
use App\Security\Handler\ChangePasswordHandler;
use App\Security\Repository\UserQueryRepositoryInterface;
use App\Shared\Event\UserPasswordChangedEvent;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class UserChangePasswordHandlerTest extends TestCase
{
    private const EMAIL = 'user@mail.com';
    private const TOKEN = 'generated.token';
    private MockObject $messageBus;
    private MockObject $userQueryRepository;
    private MockObject $tokenGenerator;
    private ChangePasswordHandler $handler;

    protected function setUp(): void
    {
        $this->messageBus = $this->createMock(MessageBusInterface::class);
        $this->userQueryRepository = $this->createMock(UserQueryRepositoryInterface::class);
        $this->tokenGenerator = $this->createMock(EmailTokenGeneratorInterface::class);

        $this->handler = new ChangePasswordHandler(
            $this->messageBus,
            $this->userQueryRepository,
            $this->tokenGenerator,
        );
    }

    public function testSuccessfulPasswordChange(): void
    {
        $userId = 1;
        $command = new ChangePasswordCommand(self::EMAIL);

        $this->userQueryRepository
            ->expects(self::once())
            ->method('getUserId')
            ->with(self::EMAIL)
            ->willReturn($userId);

        $this->tokenGenerator
            ->expects(self::once())
            ->method('generate')
            ->with(self::EMAIL, $userId)
            ->willReturn(self::TOKEN);

        $this->messageBus
            ->expects(self::once())
            ->method('dispatch')
            ->with(self::isInstanceOf(UserPasswordChangedEvent::class))
            ->willReturn(new Envelope(new UserPasswordChangedEvent(
                email: self::EMAIL,
                token: self::TOKEN,
            )));

        $this->handler->__invoke($command);
    }

    public function testUserNotFoundThrowsException(): void
    {
        $command = new ChangePasswordCommand(self::EMAIL);

        $this->userQueryRepository
            ->expects(self::once())
            ->method('getUserId')
            ->with(self::EMAIL)
            ->willReturn(null);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(ExceptionCommunicationEnum::USER_NOT_FOUND->value);

        $this->handler->__invoke($command);
    }
}
