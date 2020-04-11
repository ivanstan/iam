<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\SecurityMailerService;
use App\Service\Traits\TranslatorAwareTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/account")
 */
class AccountApiController extends AbstractApiController
{
    use TranslatorAwareTrait;

    /**
     * Request email change for current user.
     *
     * @example
     *   {
     *      "email": "user1@example.com"
     *   }
     *
     * @Route("/email", name="api_account_email", methods={"POST"})
     * @IsGranted("ROLE_USER")
     */
    public function email(UserRepository $repository, SecurityMailerService $service): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        $data = $this->getPayload();
        $email = $data['email'] ?? null;

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = [
                'status' => false,
                'error' => $this->translator->trans('Please input valid email.'),

            ];

            return new JsonResponse($error, Response::HTTP_BAD_REQUEST);
        }

        if ($repository->findByEmail($email) !== null) {
            $error = [
                'status' => false,
                'error' => $this->translator->trans('Requested email %email% is already used.', ['%email%' => $email]),
            ];

            return new JsonResponse($error, Response::HTTP_BAD_REQUEST);
        }

        $service->requestMailChange($user, $email);

        return new JsonResponse(['status' => true], Response::HTTP_OK);
    }
}
