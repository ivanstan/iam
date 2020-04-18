<?php

namespace App\Controller\Api;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api")
 */
class UserController extends AbstractApiController
{
    /**
     * @Route("/user/me", name="api_user_me", methods={"GET"})
     * @IsGranted("ROLE_USER")
     */
    public function save(Request $request): JsonResponse
    {
        return JsonResponse::fromJsonString(
            $this->serializer->serialize($this->getUser(), 'json', ['groups' => 'read'])
        );
    }
}
