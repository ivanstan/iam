<?php

namespace App\Controller;

use App\Repository\ApplicationRepository;
use App\Repository\UserRepository;
use App\Security\JwtTokenService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * @Route("/auth")
 */
class AuthController extends AbstractController
{
    protected AuthenticationUtils $utils;
    protected ApplicationRepository $repository;
    protected UserPasswordEncoderInterface $encoder;
    protected JwtTokenService $service;
    protected UserRepository $userRepository;

    public function __construct(
        AuthenticationUtils $utils,
        ApplicationRepository $applicationRepository,
        UserRepository $userRepository,
        UserPasswordEncoderInterface $encoder,
        JwtTokenService $service
    ) {
        $this->utils = $utils;
        $this->repository = $applicationRepository;
        $this->encoder = $encoder;
        $this->service = $service;
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/{uuid}/login", name="auth_login", methods={"GET", "POST"})
     */
    public function login(Request $request, string $uuid): Response
    {
        $application = $this->repository->findOneBy(['uuid' => $uuid]);
        if ($application === null) {
            throw new AccessDeniedHttpException(\sprintf('Application [%s] not found', $uuid));
        }

        if ($request->isMethod(Request::METHOD_POST)) {
            $params = $request->request->all();
            $user = $this->userRepository->findByEmail($params['email']);

            if ($user && $this->encoder->isPasswordValid($user, $params['password'])) {

                // todo check if user has authorized for this application

                $token = $this->service->issueToken($user, $application);

                return new RedirectResponse($application->getRedirect() . '?token=' . $token->toString());
            }

            $request->getSession()->getFlashBag()->set('danger', 'Invalid login credentials.');
        }

        return $this->render(
            'pages/auth.html.twig',
            [
                'application' => $application,
                'last_username' => $this->utils->getLastUsername(),
                'registration_allowed' => false,
                'error' => $this->utils->getLastAuthenticationError(),
            ]
        );
    }
}
