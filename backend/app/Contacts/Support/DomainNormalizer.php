<?php

namespace App\Contacts\Support;

final class DomainNormalizer
{
    /**
     * Normalizes a domain name: trims whitespace and lowercases it.
     */
    public static function normalize(string $domain): string
    {
        return mb_strtolower(trim($domain));
    }
}