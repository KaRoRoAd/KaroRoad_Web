<?php

declare(strict_types=1);

namespace App\Security\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
final class PasswordMatch extends Constraint
{
    public string $message;

    public function __construct(string $message = ValidationMessageEnum::PASSWORDS_DO_NOT_MATCH->value, array $options = [])
    {
        parent::__construct($options);
        $this->message = $message;
    }

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
