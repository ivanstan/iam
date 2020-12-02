<?php

namespace App\Controller\Api;

use App\Entity\Token\UserDeleteToken;
use App\Entity\Token\UserEmailChangeToken;
use App\Entity\Token\UserToken;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\SecurityMailerService;
use App\Security\SecurityService;
use App\Service\Traits\TranslatorAwareTrait;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/account")
 * ToDo: this controller will be protected by elevated user privileges.
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
    public function email(UserRepository $repository, SecurityMailerService $service, EntityManagerInterface $em): JsonResponse
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

        // remove any previous requests
        $tokens = $em->getRepository(UserToken::class)->getUserTokens($user, UserEmailChangeToken::class);
        foreach ($tokens as $token) {
            $em->remove($token);
        }
        $em->flush();

        $service->requestMailChange($user, $email);

        return new JsonResponse(['status' => true], Response::HTTP_OK);
    }

    /**
     * Deactivate current account. This will result in setting User::active to false, and logging user out of active session.
     *
     * @Route("/deactivate", name="api_account_decativate", methods={"POST"})
     * @IsGranted("ROLE_USER")
     */
    public function deactivate(EntityManagerInterface $em, SecurityService $service): RedirectResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $user->setActive(false);
        $em->flush();
        $service->logout();

        return $this->redirectToRoute('app_login');
    }

    /**
     * Request delete of current account. This will result in setting User::active to false, and logging user out of active session
     * and adding UserDeleteToken. After the token has expired account will be deleted on next cron run. If user logs in
     * before token expires, account will be restored and token will be deleted.
     *
     * @Route("/delete", name="api_account_delete", methods={"POST"})
     * @IsGranted("ROLE_USER")
     */
    public function delete(EntityManagerInterface $em, SecurityService $service): RedirectResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        // remove any previous account delete requests
        $tokens = $em->getRepository(UserToken::class)->getUserTokens($user, UserDeleteToken::class);
        foreach ($tokens as $token) {
            $em->remove($token);
        }
        $em->flush();

        $token = new UserDeleteToken($user);
        $token->setInterval(new \DateInterval('P7D'));
        $em->persist($token);
        $em->flush();

        return $this->deactivate($em, $service);
    }
}
