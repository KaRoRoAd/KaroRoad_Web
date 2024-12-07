<?php

declare(strict_types=1);

namespace App\Security\Validator;

enum ValidationMessageEnum: string
{
    case EMAIL_REQUIRED = 'Pole email nie może być puste.';
    case EMAIL_INVALID = 'Adres email jest nieprawidłowy.';
    case EMAIL_ALREADY_REGISTERED = 'Ten email jest już zarejestrowany.';
    case PASSWORD_REQUIRED = 'Pole hasło nie może być puste.';
    case PASSWORD_CONFIRM_REQUIRED = 'Pole potwierdzenia hasła nie może być puste.';
    case PASSWORDS_DO_NOT_MATCH = 'Hasła nie są zgodne.';
    case PASSWORD_STRENGTH_LENGTH = 'Hasło musi mieć co najmniej 8 znaków.';
    case PASSWORD_STRENGTH_UPPERCASE = 'Hasło musi zawierać przynajmniej jedną dużą literę.';
    case PASSWORD_STRENGTH_SPECIAL_CHARACTER = 'Hasło musi zawierać przynajmniej jeden znak specjalny.';
    case PASSWORD_STRENGTH_NUMBER = 'Hasło musi zawierać przynajmniej jedną cyfrę.';
}
