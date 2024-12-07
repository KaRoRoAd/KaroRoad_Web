<?php

declare(strict_types=1);

namespace App\Security\Validator;

use App\Security\Repository\UserQueryRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class UniqueEmailValidator extends ConstraintValidator
{
    public function __construct(
        private readonly UserQueryRepositoryInterface $userQueryRepository
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if ($this->isNullOrEmpty($value)) {
            return;
        }
        /** @var UniqueEmail $constraint */
        if ($this->isEmailNotUnique($value)) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }

    private function isNullOrEmpty(mixed $value): bool
    {
        return $value === null || $value === '';
    }

    private function isEmailNotUnique(string $value): bool
    {
        return $this->userQueryRepository->existsEmail($value);
    }
}
