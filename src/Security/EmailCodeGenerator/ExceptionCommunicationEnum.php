<?php

declare(strict_types=1);

namespace App\Security\EmailCodeGenerator;

enum ExceptionCommunicationEnum: string
{
    case TOKEN_HAS_EXPIRED = 'Token wygasł';
    case TOKEN_DOES_NOT_MATCH_EMAIL = 'Token nie pasuje do adresu e-mail.';
    case INVALID_TOKEN_SIGNATURE = 'Nieprawidłowy podpis tokenu.';
    case INVALID_TOKEN_DATA = 'Nieprawidłowe dane tokenu.';
    case INVALID_TOKEN_FORMAT = 'Nieprawidłowe typ tokenu.';
    case USER_NOT_FOUND = 'Użytkownik nie został znaleziony.';
}
