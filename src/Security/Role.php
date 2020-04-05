<?php

namespace App\Security;

use MyCLabs\Enum\Enum;

class Role extends Enum
{
    public const ADMIN = 'ROLE_ADMIN';
    public const USER = 'ROLE_USER';
}
