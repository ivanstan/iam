<?php

namespace App\Controller\Api;

use App\Repository\ApplicationRepository;
use App\Repository\UserRepository;
use App\Security\JwtTokenService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @Route("/api")
 */
class JwtController extends AbstractApiController
{
    /**
     * @Route("/token/issue", name="api_jwt_issue", methods={"POST"})
     */
    public function messages(
        Request $request,
        UserPasswordHasherInterface $encoder,
        UserRepository $userRepository,
        ApplicationRepository $applicationRepository,
        JwtTokenService $service
    ): JsonResponse {
        $this->validate(
            [
                'email' => new Assert\Email(),
                'password' => new Assert\Length(['min' => 6]),
                'app' => new Assert\Uuid(),
            ]
        );

        $params = $request->request->all();

        $app = $applicationRepository->findOneBy(['uuid' => $params['app']]);
        if ($app === null) {
            throw new AccessDeniedHttpException(\sprintf('Application [%s] not found', $params['app']));
        }

        $user = $userRepository->findByEmail($params['email']);
        if ($user === null) {
            throw new AccessDeniedHttpException(\sprintf('User [%s] not found', $params['email']));
        }

        if (!$encoder->isPasswordValid($user, $params['password'])) {
            throw new AccessDeniedHttpException('Invalid password');
        }

        $token = $service->issueToken($user, $app);

        return new JsonResponse(
            [
                'token' => $token->toString(),
            ]
        );
    }
}
