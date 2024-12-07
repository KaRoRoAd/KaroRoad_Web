<?php

declare(strict_types=1);

namespace App\Security\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
final class PasswordStrength extends Constraint
{
    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
