<?php

namespace App\Controller\Api;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/**
 * @Route("/api")
 */
class UserController extends AbstractApiController
{
    /**
     * @Route("/user/me", name="api_user_me", methods={"GET"})
     */
    public function me(): JsonResponse
    {
        return JsonResponse::fromJsonString(
            $this->serializer->serialize($this->getUser(), 'json', ['groups' => 'read'])
        );
    }

    /**
     * @Route("/user/password/change", name="api_user_password_chamge", methods={"POST"})
     * @IsGranted("ROLE_USER")
     */
    public function password(UserPasswordHasherInterface $encoder, EntityManagerInterface $em): JsonResponse
    {
        $payload = $this->getPayload();

        if (!$encoder->isPasswordValid($this->getUser(), $payload['currentPassword'])) {
            throw new BadRequestHttpException('You have entered incorrect password.');
        }

        /** @var User $user */
        $user = $this->getUser();
        $user->setPlainPassword($payload['newPassword']);
        $user->setUpdated();
        $em->flush();

        return new JsonResponse();
    }
}
