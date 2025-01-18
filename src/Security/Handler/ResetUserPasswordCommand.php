<?php

declare(strict_types=1);

namespace App\Security\Handler;

use App\Security\Validator\ValidationMessageEnum;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class ResetUserPasswordCommand
{
    public function __construct(
        #[Assert\NotBlank(message: ValidationMessageEnum::EMAIL_REQUIRED->value)]
        #[Assert\Email(message: ValidationMessageEnum::EMAIL_INVALID->value)]
        public string $email,
    ) {
    }
}
