<?php

namespace App\Controller\Api;

use App\Repository\ApplicationRepository;
use App\Repository\UserRepository;
use App\Security\JwtTokenService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;

/**
 * @Route("/api")
 */
class JwtController extends AbstractController
{
    /**
     * @Route("/token/issue", name="api_jwt_issue", methods={"POST"})
     */
    public function messages(
        Request $request,
        UserPasswordEncoderInterface $encoder,
        UserRepository $userRepository,
        ApplicationRepository $applicationRepository,
        JwtTokenService $service
    ): JsonResponse {
        $validator = Validation::createValidator();
        $params = $request->request->all();

        $violations = $validator->validate(
            $params,
            new Assert\Collection(
                [
                    'email' => new Assert\Email(),
                    'password' => new Assert\Length(['min' => 6]),
                    'app' => new Assert\Uuid(),
                ]
            )
        );

        if ($violations->count() > 0) {
            throw new BadRequestHttpException($violations->get(0)->getMessage());
        }

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
