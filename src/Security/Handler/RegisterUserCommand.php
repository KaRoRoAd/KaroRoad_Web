<?php

declare(strict_types=1);

namespace App\Security\Handler;

use App\Security\Validator\PasswordMatch;
use App\Security\Validator\PasswordStrength;
use App\Security\Validator\UniqueEmail;
use App\Security\Validator\ValidationMessageEnum;
use Symfony\Component\Validator\Constraints as Assert;

#[PasswordMatch]
final readonly class RegisterUserCommand
{
    public function __construct(
        #[Assert\NotBlank(message: ValidationMessageEnum::EMAIL_REQUIRED->value)]
        #[Assert\Email(message: ValidationMessageEnum::EMAIL_INVALID->value)]
        #[UniqueEmail]
        public string $email,
        #[Assert\NotBlank(message: ValidationMessageEnum::PASSWORD_REQUIRED->value)]
        #[PasswordStrength]
        public string $password,
        #[Assert\NotBlank(message: ValidationMessageEnum::PASSWORD_CONFIRM_REQUIRED->value)]
        public string $confirmPassword,
    ) {
    }
}
