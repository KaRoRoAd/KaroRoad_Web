<?php

declare(strict_types=1);

namespace App\Tests\Security\Unit\Handler;

use App\Security\EmailTokenGenerator\EmailTokenGeneratorInterface;
use App\Security\EmailTokenGenerator\ExceptionCommunicationEnum;
use App\Security\Entity\User;
use App\Security\Handler\ResetPasswordCommand;
use App\Security\Handler\ResetPasswordHandler;
use App\Security\Repository\UserQueryRepositoryInterface;
use App\Security\Repository\UserRepositoryInterface;
use App\Shared\Event\ResetPasswordRequestedEvent;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserResetPasswordHandlerTest extends TestCase
{
    private const EMAIL = 'user@mail.com';
    private const TOKEN = 'valid.token';
    private const PASSWORD = 'newPassword123';

    private MockObject $messageBus;
    private MockObject $userRepository;
    private MockObject $userQueryRepository;
    private MockObject $userPasswordHasher;
    private MockObject $tokenGenerator;
    private ResetPasswordHandler $handler;

    protected function setUp(): void
    {
        $this->messageBus = $this->createMock(MessageBusInterface::class);
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->userQueryRepository = $this->createMock(UserQueryRepositoryInterface::class);
        $this->userPasswordHasher = $this->createMock(UserPasswordHasherInterface::class);
        $this->tokenGenerator = $this->createMock(EmailTokenGeneratorInterface::class);

        $this->handler = new ResetPasswordHandler(
            $this->messageBus,
            $this->userRepository,
            $this->userQueryRepository,
            $this->userPasswordHasher,
            $this->tokenGenerator
        );
    }

    public function testSuccessfulPasswordReset(): void
    {
        $user = new User(self::EMAIL);
        $user->setId(1);

        $decodedToken = [
            'email' => self::EMAIL,
            'userId' => 1,
            'expired' => time() + 3600,
        ];

        $this->tokenGenerator
            ->expects($this->once())
            ->method('decode')
            ->with(self::TOKEN)
            ->willReturn($decodedToken);

        $this->userQueryRepository
            ->expects($this->once())
            ->method('findOneByEmail')
            ->with(self::EMAIL)
            ->willReturn($user);

        $this->userPasswordHasher
            ->expects($this->once())
            ->method('hashPassword')
            ->with($user, self::PASSWORD)
            ->willReturn('hashedPassword');

        $this->userRepository
            ->expects($this->once())
            ->method('save')
            ->with($user);

        $this->tokenGenerator
            ->expects($this->once())
            ->method('generate')
            ->with(self::EMAIL, $user->getId())
            ->willReturn('newGeneratedToken');

        $this->messageBus->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(ResetPasswordRequestedEvent::class))
            ->willReturn(new Envelope(new ResetPasswordRequestedEvent(
                email: self::EMAIL,
                password: self::PASSWORD,
                passwordConfirmation: self::PASSWORD,
                token: 'newGeneratedToken'
            )));

        $command = new ResetPasswordCommand(
            email: self::EMAIL,
            password: self::PASSWORD,
            confirmPassword: self::PASSWORD,
            token: self::TOKEN
        );

        $this->handler->__invoke($command);
    }

    public function testExpiredTokenThrowsException(): void
    {
        $decodedToken = [
            'email' => self::EMAIL,
            'userId' => 1,
            'expired' => time() - 3600,
        ];

        $this->tokenGenerator
            ->expects($this->once())
            ->method('decode')
            ->with(self::TOKEN)
            ->willReturn($decodedToken);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage(ExceptionCommunicationEnum::TOKEN_HAS_EXPIRED->value);

        $command = new ResetPasswordCommand(
            email: self::EMAIL,
            password: self::PASSWORD,
            confirmPassword: self::PASSWORD,
            token: self::TOKEN
        );

        $this->handler->__invoke($command);
    }

    public function testTokenEmailMismatchThrowsException(): void
    {
        $decodedToken = [
            'email' => 'other@mail.com',
            'userId' => 1,
            'expired' => time() + 3600,
        ];

        $this->tokenGenerator
            ->expects($this->once())
            ->method('decode')
            ->with(self::TOKEN)
            ->willReturn($decodedToken);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage(ExceptionCommunicationEnum::TOKEN_DOES_NOT_MATCH_EMAIL->value);

        $command = new ResetPasswordCommand(
            email: self::EMAIL,
            password: self::PASSWORD,
            confirmPassword: self::PASSWORD,
            token: self::TOKEN
        );

        $this->handler->__invoke($command);
    }

    public function testUserNotFoundThrowsException(): void
    {
        $decodedToken = [
            'email' => self::EMAIL,
            'userId' => 1,
            'expired' => time() + 3600,
        ];

        $this->tokenGenerator
            ->expects($this->once())
            ->method('decode')
            ->with(self::TOKEN)
            ->willReturn($decodedToken);

        $this->userQueryRepository
            ->expects($this->once())
            ->method('findOneByEmail')
            ->with(self::EMAIL)
            ->willReturn(null);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage(ExceptionCommunicationEnum::USER_NOT_FOUND->value);

        $command = new ResetPasswordCommand(
            email: self::EMAIL,
            password: self::PASSWORD,
            confirmPassword: self::PASSWORD,
            token: self::TOKEN
        );

        $this->handler->__invoke($command);
    }
}
