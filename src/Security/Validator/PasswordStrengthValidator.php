<?php

declare(strict_types=1);

namespace App\Security\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class PasswordStrengthValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        if ($this->isNullOrEmpty($value)) {
            return;
        }

        $errors = $this->checkPasswordStrength($value);

        foreach ($errors as $error) {
            $this->context->buildViolation($error)
                ->addViolation();
        }
    }

    private function isNullOrEmpty(mixed $value): bool
    {
        return $value === null || $value === '';
    }

    private function checkPasswordStrength(string $value): array
    {
        $errors = [];
        if ($this->isTooShort($value)) {
            $errors[] = ValidationMessageEnum::PASSWORD_STRENGTH_LENGTH->value;
        }
        if ($this->isMissingUppercase($value)) {
            $errors[] = ValidationMessageEnum::PASSWORD_STRENGTH_UPPERCASE->value;
        }
        if ($this->isMissingSpecialCharacter($value)) {
            $errors[] = ValidationMessageEnum::PASSWORD_STRENGTH_SPECIAL_CHARACTER->value;
        }
        if ($this->isMissingNumber($value)) {
            $errors[] = ValidationMessageEnum::PASSWORD_STRENGTH_NUMBER->value;
        }

        return $errors;
    }

    private function isTooShort(string $value): bool
    {
        return strlen($value) < 8;
    }

    private function isMissingUppercase(string $value): bool
    {
        return ! preg_match('/[A-Z]/', $value);
    }

    private function isMissingSpecialCharacter(string $value): bool
    {
        return ! preg_match('/\W/', $value);
    }

    private function isMissingNumber(string $value): bool
    {
        return ! preg_match('/[0-9]/', $value);
    }
}
