<?php

namespace App\Contacts\Support;

final class EmailNormalizer
{
    /**
     * Normalizes an email address: trims whitespace and lowercases it.
     * Plus aliases (user+tag@domain) are intentionally kept as distinct addresses.
     */
    public static function normalize(string $email): string
    {
        return mb_strtolower(trim($email));
    }
}