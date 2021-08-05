<?php

namespace App\Controller;

use App\Repository\ApplicationRepository;
use App\Repository\UserRepository;
use App\Security\JwtTokenService;
use App\Security\TokenAuthenticator;
use App\Security\UserProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[Route('/auth')]
class AuthController extends AbstractController
{
    public function __construct(
        protected AuthenticationUtils $utils,
        protected ApplicationRepository $applicationRepository,
        protected UserRepository $userRepository,
        protected UserPasswordHasherInterface $encoder,
        protected JwtTokenService $service
    ) {
    }

    #[Route('/{uuid}/login', name: 'auth_login', methods: ['GET', 'POST'])]
    public function login(Request $request, string $uuid): Response
    {
        $application = $this->applicationRepository->findOneBy(['uuid' => $uuid]);
        if ($application === null) {
            throw new AccessDeniedHttpException(\sprintf('Application [%s] not found', $uuid));
        }

        if ($request->isMethod(Request::METHOD_POST)) {
            $params = $request->request->all();
            $user = $this->userRepository->findByEmail($params['email']);

            if ($user && $this->encoder->isPasswordValid($user, $params['password'])
                && $this->applicationRepository->userIsMemberOfApplication($user, $application)
            ) {
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

    #[Route('/authorize', name: 'security_authorize')]
    public function authorize(
        Request $request,
        UserProvider $provider
    ): Response {
        $token = $request->get(TokenAuthenticator::COOKIE_NAME);

        if (!$provider->isValid($token)) {
            $this->addFlash('warning', 'Please login to continue.');
            $this->redirect($this->generateUrl('security_login'));
        }

        $response = $this->redirect($this->generateUrl('app_index'));
        $response->headers->setCookie(
            Cookie::create(TokenAuthenticator::COOKIE_NAME)
                ->withValue($token)
                ->withSecure(true)
        );

        return $response;
    }
}
