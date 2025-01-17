<?php

declare(strict_types=1);

namespace App\Tests\Security\Unit\Handler;

use App\Security\EmailTokenGenerator\EmailTokenGeneratorInterface;
use App\Security\EmailTokenGenerator\ExceptionCommunicationEnum;
use App\Security\Entity\User;
use App\Security\Handler\VerifyUserCommand;
use App\Security\Handler\VerifyUserHandler;
use App\Security\Repository\UserQueryRepositoryInterface;
use App\Security\Repository\UserRepositoryInterface;
use App\Shared\Event\UserVerifiedEvent;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class VerifyUserHandlerTest extends TestCase
{
    private const TOKEN = 'valid.token';

    private const EMAIL = 'email@mail.com';
    private MockObject $messageBus;
    private MockObject $userRepository;
    private MockObject $userQueryRepository;
    private MockObject $tokenGenerator;
    private VerifyUserHandler $handler;

    protected function setUp(): void
    {
        $this->messageBus = $this->createMock(MessageBusInterface::class);
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->userQueryRepository = $this->createMock(UserQueryRepositoryInterface::class);
        $this->tokenGenerator = $this->createMock(EmailTokenGeneratorInterface::class);

        $this->handler = new VerifyUserHandler(
            $this->messageBus,
            $this->userRepository,
            $this->userQueryRepository,
            $this->tokenGenerator
        );
    }

    /**
     * @throws Exception
     */
    public function testSuccessfulVerification(): void
    {
        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $userQueryRepository = $this->createMock(UserQueryRepositoryInterface::class);
        $messageBus = $this->createMock(MessageBusInterface::class);
        $tokenGenerator = $this->createMock(EmailTokenGeneratorInterface::class);

        $user = new User(email: self::EMAIL);
        $user->setId(1);

        $decodedToken = [
            'email' => self::EMAIL,
            'userId' => 1,
            'expired' => time() + 3600,
        ];

        $tokenGenerator
            ->expects(self::once())
            ->method('decode')
            ->with(self::TOKEN)
            ->willReturn($decodedToken);

        $userQueryRepository
            ->expects(self::once())
            ->method('findOneByEmail')
            ->with(self::EMAIL)
            ->willReturn($user);

        $userRepository
            ->expects(self::once())
            ->method('save')
            ->with($user);

        $messageBus->expects(self::once())
            ->method('dispatch')
            ->with(self::isInstanceOf(UserVerifiedEvent::class))
            ->willReturn(new Envelope(new UserVerifiedEvent(
                email: self::EMAIL,
                token: self::TOKEN
            )));

        $handler = new VerifyUserHandler(
            $messageBus,
            $userRepository,
            $userQueryRepository,
            $tokenGenerator
        );

        $handler(new VerifyUserCommand(
            email: self::EMAIL,
            token: self::TOKEN
        ));
    }

    public function testExpiredTokenThrowsException(): void
    {
        $email = 'user@mail.com';
        $token = 'expired.token';
        $userId = 1;

        $command = new VerifyUserCommand($email, $token);

        $decodedToken = [
            'email' => $email,
            'userId' => $userId,
            'expired' => time() - 3600,
        ];

        $this->tokenGenerator
            ->expects($this->once())
            ->method('decode')
            ->with($token)
            ->willReturn($decodedToken);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage(ExceptionCommunicationEnum::TOKEN_HAS_EXPIRED->value);

        $this->handler->__invoke($command);
    }

    public function testTokenEmailMismatchThrowsException(): void
    {
        $email = 'user@mail.com';
        $token = 'mismatched.token';
        $userId = 1;

        $command = new VerifyUserCommand($email, $token);

        $decodedToken = [
            'email' => 'other@mail.com',
            'userId' => $userId,
            'expired' => time() + 3600,
        ];

        $this->tokenGenerator
            ->expects($this->once())
            ->method('decode')
            ->with($token)
            ->willReturn($decodedToken);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage(ExceptionCommunicationEnum::TOKEN_DOES_NOT_MATCH_EMAIL->value);

        $this->handler->__invoke($command);
    }

    public function testUserNotFoundThrowsException(): void
    {
        $email = 'user@mail.com';
        $token = 'valid.token';

        $command = new VerifyUserCommand($email, $token);

        $decodedToken = [
            'email' => $email,
            'userId' => 1,
            'expired' => time() + 3600,
        ];

        $this->tokenGenerator
            ->expects($this->once())
            ->method('decode')
            ->with($token)
            ->willReturn($decodedToken);

        $this->userQueryRepository
            ->expects($this->once())
            ->method('findOneByEmail')
            ->with($email)
            ->willReturn(null);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage(ExceptionCommunicationEnum::USER_NOT_FOUND->value);

        $this->handler->__invoke($command);
    }
}
