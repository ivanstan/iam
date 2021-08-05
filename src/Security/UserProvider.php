<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Signer\Key\LocalFileReference;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
    protected ?Configuration $configuration = null;

    public function __construct(
        protected $projectDir,
        protected $env,
        protected UserRepository $repository
    ) {
    }

    /**
     * Symfony calls this method if you use features like switch_user
     * or remember_me.
     *
     * If you're not using these features, you do not need to implement
     * this method.
     *
     * @return UserInterface
     *
     * @throws UsernameNotFoundException if the user is not found
     */
    public function loadUserByUsername(string $token)
    {
        dd($token);

        // Load a User object from your data source or throw UsernameNotFoundException.
        // The $username argument may not actually be a username:
        // it is whatever value is being returned by the getUsername()
        // method in your User class.

        return $this->getUser($token);
    }

    public function getUser(string $token): ?UserInterface
    {
        $parsed = $this->getConfig()->parser()->parse($token);

        if ($data = $parsed->claims()->get('user')) {
            return $this->repository->find($data['id']);
        }

        return null;
    }

    public function getConfig(): Configuration
    {
        if ($this->configuration === null) {
            $this->configuration = Configuration::forAsymmetricSigner(
                new Signer\Rsa\Sha512(),
                LocalFileReference::file($this->projectDir . '/config/secrets/' . $this->env . '/jwtRS256.key'),
                LocalFileReference::file($this->projectDir . '/config/secrets/' . $this->env . '/jwtRS256.key.pub'),
            );
        }

        return $this->configuration;
    }

    public function supportsClass(string $class)
    {
        return User::class === $class || is_subclass_of($class, User::class);
    }

    public function refreshUser(UserInterface $user)
    {
    }

    public function isValid(?string $token): bool
    {
        if ($token === null) {
            return false;
        }

        try {
            $this->getConfig()->validator()->assert($this->getConfig()->parser()->parse($token), ...$this->getValidationConstraints());
        } catch (\Exception $exception) {

            dd($exception->getMessage());

            return false;
        }

        return true;
    }

    private function getValidationConstraints(): array
    {
        return [
//            new \Lcobucci\JWT\Validation\Constraint\IssuedBy('https://iam.ivanstanojevic.me/'),
            new \Lcobucci\JWT\Validation\Constraint\SignedWith(
                $this->getConfig()->signer(),
                $this->getConfig()->verificationKey()
            ),
        ];
    }

    public function loadUserByIdentifier(string $identifier): ?UserInterface
    {
        return $this->getUser($identifier);
    }
}
