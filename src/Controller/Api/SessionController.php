<?php

namespace App\Controller\Api;

use App\Entity\Session;
use App\Entity\User;
use App\Repository\SessionRepository;
use App\Security\Role;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @Route("/sessions/{user}", name="api_session_user", methods={"GET"})
     * @IsGranted("ROLE_USER")
     */
    public function sessions(User $user, SessionRepository $repository, SerializerInterface $serializer): Response
    {
        if ($user->getId() !== $this->getUser()->getId() && !$this->isGranted(Role::ADMIN)) {
            throw new AccessDeniedHttpException('Only administrators can view other user\'s sessions.');
        }

        $sessions =  $repository->getUserSessions($user);

        return new Response($serializer->serialize(
            $sessions->getQuery()->getResult(), 'json', ['groups' => ['read']]
        ));
    }

    /**
     * Get active sessions
     *
     * @Route("/session/{session}", name="api_session_delete", methods={"DELETE"})
     * @IsGranted("ROLE_USER")
     */
    public function delete(Session $session, EntityManagerInterface $em): JsonResponse
    {
        if ($session->getUser()->getId() !== $this->getUser()->getId() && !$this->isGranted(Role::ADMIN)) {
            throw new AccessDeniedHttpException('Only administrators can delete other user\'s sessions.');
        }

        $em->remove($session);
        $em->flush();

        return new JsonResponse();
    }
}
