<?php

namespace App\Entity\Token;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class UserEmailChangeToken extends UserToken
{
    public const TYPE = 'email.change';
}
