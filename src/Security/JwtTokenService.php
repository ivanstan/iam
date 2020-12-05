<?php

namespace App\Security;

use App\Entity\User;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Signer\Key\LocalFileReference;
use Lcobucci\JWT\Token;

class JwtTokenService
{
    protected ?Configuration $configuration = null;
    protected string $projectDir;

    public function __construct($projectDir)
    {
        $this->projectDir = $projectDir;
    }

    public function issueToken(User $user)
    {
        $this->getConfig();

        $now = new \DateTimeImmutable();

        return $this->getConfig()->builder()
            // Configures the issuer (iss claim)
            ->issuedBy('http://example.com')
            // Configures the audience (aud claim)
            ->permittedFor('http://example.org')
            // Configures the id (jti claim)
            ->identifiedBy('4f1g23a12aa')
            // Configures the time that the token was issue (iat claim)
            ->issuedAt($now)
            // Configures the time that the token can be used (nbf claim)
            ->canOnlyBeUsedAfter($now)
            // Configures the expiration time of the token (exp claim)
            ->expiresAt($now->modify('+2 months'))
            // Configures a new claim, called "uid"
            ->withClaim('uid', $user->getId())
            // Configures a new header, called "foo"
            ->withHeader('foo', 'bar')
            // Builds a new token
            ->getToken($this->getConfig()->signer(), $this->getConfig()->signingKey());
    }

    public function getConfig(): Configuration
    {
        if ($this->configuration === null) {
            $this->configuration = Configuration::forAsymmetricSigner(
                new Signer\Rsa\Sha512(),
                LocalFileReference::file($this->projectDir . '/config/secrets/dev/jwtRS256.key'),
                LocalFileReference::file($this->projectDir . '/config/secrets/dev/jwtRS256.key.pub'),
            );
        }

        return $this->configuration;
    }

    public function parse(string $plain): Token
    {
        $token = $this->getConfig()->parser()->parse($plain);

        $this->getConfig()->setValidationConstraints(
            new \Lcobucci\JWT\Validation\Constraint\SignedWith($this->getConfig()->signer(), $this->getConfig()->verificationKey())
        );

        $constraints = $this->getConfig()->validationConstraints();

        if (!$this->getConfig()->validator()->validate($token, ...$constraints)) {
            throw new \RuntimeException('No way!');
        }

        return $token;
    }
}
