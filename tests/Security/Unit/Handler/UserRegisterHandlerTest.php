<?php

declare(strict_types=1);

namespace App\Tests\Security\Unit\Handler;

use App\Security\EmailTokenGenerator\EmailTokenGeneratorInterface;
use App\Security\Entity\User;
use App\Security\Handler\RegisterUserCommand;
use App\Security\Handler\RegisterUserHandler;
use App\Security\Repository\UserRepositoryInterface;
use App\Shared\Event\UserRegisteredEvent;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserRegisterHandlerTest extends TestCase
{
    private const PASSWORD = 'password';
    private const EMAIL = 'email@mail.com';

    /**
     * @throws Exception
     * @throws ExceptionInterface
     */
    public function testCanHandleUserRegister(): void
    {
        $userRepository = $this->createMock(UserRepositoryInterface::class);
        $messageBus = $this->createMock(MessageBusInterface::class);
        $passwordHasher = $this->createMock(UserPasswordHasherInterface::class);
        $emailTokenGenerator = $this->createMock(EmailTokenGeneratorInterface::class);

        $user = new User(email: self::EMAIL, password: self::PASSWORD);
        $user->setId(1);

        $userRepository
            ->expects(self::once())
            ->method('save')
            ->willReturn($user);

        $passwordHasher
            ->expects(self::once())
            ->method('hashPassword')
            ->with(user: self::anything(), password: self::anything());

        $emailTokenGenerator
            ->expects(self::once())
            ->method('generate')
            ->with(self::EMAIL, 1)
            ->willReturn('token');

        $messageBus->expects(self::once())
            ->method('dispatch')
            ->with(self::isInstanceOf(UserRegisteredEvent::class))
            ->willReturn(new Envelope(new UserRegisteredEvent(
                userId: 1,
                name: 'name',
                surname: 'surname',
                email: self::EMAIL,
                phoneNumber: 'phoneNumber',
                agree: true,
            )));

        $handler = new RegisterUserHandler(
            $messageBus,
            $userRepository,
            $passwordHasher,
            $emailTokenGenerator,
        );

        $handler(new RegisterUserCommand(
            name: 'name',
            surname: 'surname',
            email: self::EMAIL,
            phoneNumber: 'phoneNumber',
            password: self::PASSWORD,
            confirmPassword: self::PASSWORD,
            agree: true,
        ));
    }
}
