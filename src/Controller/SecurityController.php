<?php

namespace App\Controller;

use App\Entity\Token\UserRecoveryToken;
use App\Entity\Token\UserToken;
use App\Entity\User;
use App\Form\PasswordRepeatType;
use App\Form\RegisterType;
use App\Repository\SettingsRepository;
use App\Security\Role;
use App\Security\SecurityMailerService;
use App\Security\SecurityService;
use App\Service\DateTimeService;
use App\Service\Traits\TranslatorAwareTrait;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\Constraints\Email;

class SecurityController extends AbstractController implements LoggerAwareInterface
{
    use TranslatorAwareTrait;
    use LoggerAwareTrait;

    private SecurityMailerService $securityMailer;
    private SecurityService $securityService;

    public function __construct(
        SecurityMailerService $securityMailer,
        SecurityService $securityService
    ) {
        $this->securityMailer = $securityMailer;
        $this->securityService = $securityService;
    }

    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils, SettingsRepository $repository): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_index');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render(
            'pages/security/login.html.twig',
            [
                'last_username' => $lastUsername,
                'error' => $error,
                'registration_allowed' => $repository->isRegistrationEnabled(),
            ]
        );
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/register", name="security_register")
     */
    public function register(Request $request, SettingsRepository $repository): Response
    {
        if (!$repository->isRegistrationEnabled()) {
            throw new NotFoundHttpException();
        }

        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $user->setActive(true);
            $user->setVerified(false);
            $user->setRoles([Role::USER]);
            $em->persist($user);
            $em->flush();

            $this->logger->info(sprintf('New user %s has registered', $user->getEmail()));

            $this->securityMailer->requestVerification($user);
            $this->addFlash('success', $this->translator->trans('user.messages.register.success'));

            return $this->redirectToRoute('app_login');
        }

        return $this->render(
            'pages/security/register.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/recovery", name="security_recovery")
     */
    public function recovery(Request $request): Response
    {
        $form = $this->createFormBuilder()->add(
            'email',
            EmailType::class,
            [
                'constraints' => [new Email()],
                'label' => false,
                'required' => true,
                'attr' => ['placeholder' => 'Email', 'data-test' => 'email'],
            ]
        )->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $data['email']]);

            if ($user) {
                $this->securityMailer->requestRecovery($user);
                $this->logger->info(sprintf('User %s has requested password recovery', $user->getEmail()));
            }

            $this->addFlash('success', $this->translator->trans('user.messages.recovery.request'));

            return $this->redirectToRoute('app_login');
        }

        return $this->render(
            'pages/security/recovery.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/verify", name="security_verify", methods={"POST"})
     */
    public function verify(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$this->isCsrfTokenValid('verify' . $user->getId(), $request->request->get('_token'))) {
            return $this->redirectToRoute('app_index');
        }

        if (!$user->isVerified()) {
            $this->logger->info(sprintf('User %s requested verification', $user->getEmail()));

            $this->addFlash(
                'info',
                $this->translator->trans(
                    'user.messages.verify.request',
                    [
                        '%email%' => $user->getEmail(),
                    ]
                )
            );

            $this->securityMailer->requestVerification($user);
        }

        return $this->redirectToRoute('app_index');
    }

    /**
     * @Route("/password/recover/{token}", name="security_recovery_token")
     * @Route("/verify/{token}", name="security_verification_token")
     * @Route("/invitation/{token}", name="security_invitation_token")
     */
    public function verifyToken(Request $request, string $token): RedirectResponse
    {
        if ($this->getUser() !== null) {
            $this->addFlash(
                'warning',
                $this->translator->trans(
                    'You are already have active session. Please logout before using provided url.',
                    )
            );

            return $this->redirectToRoute('app_index');
        }

        switch ($request->get('_route')) {
            case 'security_verification_token': // verification
                $redirect = 'app_index';
                $failMessageId = 'user.messages.verify.bad_token';
                $successMessage = $this->translator->trans('user.messages.verify.success');
                $logMessage = 'User %s has verified';
                $user = $this->securityService->verify($token);
                break;
            case 'security_recovery_token': // recovery
                $redirect = 'app_user_password_recover';
                $failMessageId = 'user.messages.recovery.bad_token';
                $successMessage = $this->translator->trans('user.messages.recovery.success');
                $logMessage = 'User %s has used login token';
                $user = $this->securityService->recover($token);
                break;
            default: // invitation
                $redirect = 'app_user_password';
                $failMessageId = 'user.messages.invitation.bad_token';
                $successMessage = $this->translator->trans('user.messages.invitation.success');
                $logMessage = 'User %s has received invitation and verified account';
                $user = $this->securityService->recover($token);
        }

        if ($user === null) {
            $this->addFlash(
                'danger',
                $this->translator->trans(
                    $failMessageId,
                    [
                        '%url%' => $this->generateUrl('security_recovery'),
                    ]
                )
            );

            return $this->redirectToRoute('security_recovery');
        }

        $this->logger->info(sprintf($logMessage, $user->getEmail()));

        $this->addFlash('success', $successMessage);

        return $this->redirectToRoute($redirect);
    }

    /**
     * @Route("/user/password/recover", name="app_user_password_recover")
     * @IsGranted("ROLE_USER")
     */
    public function recover(Request $request, EntityManagerInterface $em, string $token = null): Response
    {
        $user = $this->getUser();

        if ($user === null) {
            $this->addFlash('warning', $this->translator->trans('security.token.invalid'));

            return $this->redirectToRoute('security_recovery');
        }

        $form = $this->createForm(PasswordRepeatType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $user->setPlainPassword($data);
            $user->setVerified(true);
            $user->setUpdated();

            $em->flush();

            $this->addFlash('success', $this->translator->trans('user.password.changed'));

            return $this->redirectToRoute('app_index');
        }

        return $this->render(
            'pages/security/reset.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/user/password", name="app_user_password")
     * @IsGranted("ROLE_USER")
     */
    public function password(Request $request, EntityManagerInterface $em): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if ($user->getPassword() !== null) {
            return $this->redirectToRoute('app_index');
        }

        $form = $this->createForm(PasswordRepeatType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $user->setPlainPassword($data);
            $user->setVerified(true);
            $user->setUpdated();
            $em->flush();

            $this->addFlash('success', $this->translator->trans('user.password.changed'));

            return $this->redirectToRoute('app_index');
        }

        return $this->render(
            'pages/security/reset.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }
}
