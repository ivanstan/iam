<?php

namespace App\Entity\Token;

use App\Entity\User;
use App\Repository\UserTokenRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserTokenRepository::class)]
#[ORM\InheritanceType(('SINGLE_TABLE'))]
#[ORM\DiscriminatorColumn(name: 'type', type: 'string')]
#[ORM\DiscriminatorMap([
    UserVerificationToken::TYPE => UserVerificationToken::class,
    UserRecoveryToken::TYPE => UserRecoveryToken::class,
    UserInvitationToken::TYPE => UserInvitationToken::class,
    UserAccessToken::TYPE => UserAccessToken::class,
    UserEmailChangeToken::TYPE => UserEmailChangeToken::class,
    UserDeleteToken::TYPE => UserDeleteToken::class,
])]
abstract class UserToken extends Token
{
    public const TYPE = 'user';

    /**
     * @var User
     */
    #[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    protected User $user;

    #[ORM\Column(type: 'string', nullable: true)]
    protected $data;

    /**
     * @throws \Exception
     */
    public function __construct(User $user = null)
    {
        parent::__construct();

        if ($user !== null) {
            $this->user = $user;
        }
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData($data): void
    {
        $this->data = $data;
    }
}
