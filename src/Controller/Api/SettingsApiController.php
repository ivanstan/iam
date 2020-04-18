<?php

namespace App\Controller\Api;

use App\Repository\SettingsRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api")
 */
class SettingsApiController extends AbstractApiController
{
    /**
     * @Route("/settings", name="api_settings_collection", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function collection(Request $request, SettingsRepository $repository): Response
    {
        return $this->response(
            $this->serializer->serialize($repository->findAll(), 'json', ['groups' => 'read'])
        );
    }

    /**
     * @Route("/settings", name="api_settings_save", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function save(Request $request): JsonResponse
    {
        return new JsonResponse();
    }
}
