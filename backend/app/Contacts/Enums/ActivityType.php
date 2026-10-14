<?php

namespace App\Contacts\Enums;

enum ActivityType: string
{
    case Note       = 'note';
    case Call       = 'call';
    case Email      = 'email';
    case Meeting    = 'meeting';
}