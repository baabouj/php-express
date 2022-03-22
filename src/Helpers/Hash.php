<?php

namespace Pexess\Helpers;

class Hash
{
    public static function hash(string $secret): string
    {
        return password_hash($secret, PASSWORD_ARGON2ID);
    }

    public static function verify($secret, $hash): bool
    {
        return password_verify($secret, $hash);
    }
}