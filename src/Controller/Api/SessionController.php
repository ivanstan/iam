<?php

namespace App\Controller\Api;

use App\Entity\Session;
use App\Entity\User;
use App\Model\Api\Collection;
use App\Model\CollectionService;
use App\Security\Role;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/api")
 */
class SessionController extends AbstractApiController
{
    /**
     * Get active sessions
     *
     * @Route("/sessions", name="api_sessions", methods={"GET"})
     * @Route("/sessions/{user}", name="api_session_user", methods={"GET"})
     * @IsGranted("ROLE_USER")
     */
    public function sessions(
        Request $request,
        CollectionService $repository,
        SerializerInterface $serializer,
        User $user = null
    ): Response {
        if ($user === null && !$this->isGranted(Role::ADMIN)) {
            throw new AccessDeniedHttpException('Only administrators can view other user\'s sessions.');
        }

        $collection = new Collection();

        $collection->setPage($request->request->get('page', 1));

        $collection->setEntity(Session::class);

        $collection = $repository->collection($collection);
        $collection->setRoute('api_sessions');

        $collection = $repository->normalize($collection, $request);

        return new JsonResponse($collection);
    }

    /**
     * Get active sessions
     *
     * @Route("/sessions/{user}", name="api_session_delete", methods={"DELETE"})
     * @IsGranted("ROLE_USER")
     */
    public function delete(User $user = null): JsonResponse
    {
        return new JsonResponse();
    }
}
