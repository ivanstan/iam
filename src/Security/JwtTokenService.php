<?php

namespace App\Security;

use App\Entity\Application;
use App\Entity\User;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Signer\Key\LocalFileReference;
use Lcobucci\JWT\Token;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Uid\Uuid;

class JwtTokenService
{
    protected ?Configuration $configuration = null;
    protected string $projectDir;
    protected NormalizerInterface $normalizer;
    protected RouterInterface $router;
    protected string $env;

    public function __construct($projectDir, $env, NormalizerInterface $normalizer, RouterInterface $router)
    {
        $this->projectDir = $projectDir;
        $this->normalizer = $normalizer;
        $this->router = $router;
        $this->env = $env;
    }

    public function issueToken(User $user, Application $application)
    {
        $this->getConfig();

        $now = new \DateTimeImmutable();

        return $this->getConfig()->builder()
            ->issuedBy($this->router->generate('app_index', [], UrlGeneratorInterface::ABSOLUTE_URL))
            ->permittedFor($application->getUrl())
            ->identifiedBy(Uuid::v4())
            ->issuedAt($now)
            ->canOnlyBeUsedAfter($now)
            ->expiresAt($now->modify('+2 months'))
            ->withClaim('user', $this->normalizer->normalize($user, null, ['groups' => 'jwt']))
            // Configures a new header, called "foo"
            //            ->withHeader('foo', 'bar')
            ->getToken($this->getConfig()->signer(), $this->getConfig()->signingKey());
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
