<?php

namespace App\Controller\Api;

use App\Entity\Settings;
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
        /** @var SettingsRepository $repository */
        $repository = $this->em->getRepository(Settings::class);

        foreach ($this->getPayload() as $data) {
            if ($setting = $repository->getSettings($data['namespace'])->getEntity($data['name'])) {
                $setting->setValue($data['value']);
            }
        }

        $this->em->flush();

        return new JsonResponse();
    }
}
