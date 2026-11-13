<?php

declare(strict_types=1);

namespace Crm\Core;

final class Csrf
{
    public static function token(): string
    {
        return $_SESSION['_token'] ??= bin2hex(random_bytes(32));
    }

    public static function validate(?string $token): bool
    {
        return is_string($token)
            && isset($_SESSION['_token'])
            && hash_equals($_SESSION['_token'], $token);
    }
}
