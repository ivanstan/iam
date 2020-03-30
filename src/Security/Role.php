<?php

namespace App\Security;

use MyCLabs\Enum\Enum;

class Role extends Enum
{
    public const USER = 'ROLE_USER';
    public const ADMIN = 'ROLE_ADMIN';
}
