<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api")
 */
class FlashMessageController extends AbstractController
{
    /**
     * @Route("/messages", name="api_flash_messages", methods={"GET"})
     */
    public function messages(Request $request): JsonResponse
    {
        return new JsonResponse($request->getSession()->getFlashBag()->all());
    }
}
