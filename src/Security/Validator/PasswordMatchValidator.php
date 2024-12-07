<?php

declare(strict_types=1);

namespace App\Security\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class PasswordMatchValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        /** @var PasswordMatch $constraint */
        if (! $this->isPasswordMatching($value->password, $value->confirmPassword)) {
            $this->context->buildViolation($constraint->message)
                ->atPath('confirmPassword')
                ->addViolation();
        }
    }

    private function isPasswordMatching(string $password, string $confirmPassword): bool
    {
        return $password === $confirmPassword;
    }
}
