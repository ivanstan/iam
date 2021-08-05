<?php

namespace App\Entity\Token;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class UserAccessToken extends UserToken
{
    public const TYPE = 'access';
}
