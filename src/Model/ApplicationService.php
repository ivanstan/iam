<?php

namespace App\Model;

use App\Entity\Application;
use App\Repository\ApplicationRepository;
use Symfony\Component\Routing\RouterInterface;

class ApplicationService
{
    public const IAM_APP_UUID = '1a66b49a-f601-11eb-9a03-0242ac130003';

    protected Application $application;

    public function __construct(protected RouterInterface $router, protected ApplicationRepository $repository)
    {
    }

    public function getIam(): Application
    {
        return $this->repository->findOneBy(['uuid' => self::IAM_APP_UUID]);
    }

    public function set(Application $application): self
    {
        $this->application = $application;

        return $this;
    }

    public function getLoginUrl(): string
    {
        $params = [
            'uuid' => $this->application->getUuid(),
        ];

        return $this->router->generate('auth_login', $params, RouterInterface::ABSOLUTE_URL);
    }
}
