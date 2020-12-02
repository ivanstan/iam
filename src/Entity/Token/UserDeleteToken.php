<?php

namespace App\Entity\Token;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class UserDeleteToken extends UserToken
{
    public const TYPE = 'user.delete';
}
