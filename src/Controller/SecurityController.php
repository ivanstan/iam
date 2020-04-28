<?php

namespace App\Controller;

use App\Entity\Token\UserEmailChangeToken;
use App\Entity\Token\UserToken;
use App\Entity\User;
use App\Form\PasswordRepeatType;
use App\Form\RegistrationForm;
use App\Repository\SettingsRepository;
use App\Security\Role;
use App\Security\SecurityMailerService;
use App\Security\SecurityService;
use App\Service\DateTimeService;
use App\Service\Traits\LoggerAwareTrait;
use App\Service\Traits\TranslatorAwareTrait;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerAwareInterface;
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

final class SecurityController extends AbstractController implements LoggerAwareInterface
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
        $form = $this->createForm(RegistrationForm::class, $user);
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
            $this->addFlash('success', $this->translator->trans('You have registered successfully.'));

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

            $this->addFlash(
                'success',
                $this->translator->trans(
                    'You have requested account recovery. Email will be sent to: %email% with further instructions.',
                    ['email' => $data['email']]
                )
            );

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
     * Endpoint that enables user to request new verification email. Redirects to app_index.
     *
     * @Route("/verify", name="security_verify", methods={"POST"})
     * @IsGranted("ROLE_USER")
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
                    'Verification mail with further instructions is sent to %email%.',
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
                $failMessage = 'Verification link is not valid or expired';
                $successMessage = 'Your account is successfully verified.';
                $logMessage = 'User %s has verified';
                $user = $this->securityService->verify($token);
                break;
            case 'security_recovery_token': // recovery
                $redirect = 'app_user_password_recover';
                $failMessage = 'Recovery link is not valid or expired';
                $successMessage = 'You have successfully recovered your account.';
                $logMessage = 'User %s has used login token';
                $user = $this->securityService->recover($token);
                break;
            default: // invitation
                $redirect = 'app_user_password';
                $failMessage = 'Invitation is not valid or expired';
                $successMessage = 'You have finished invitation successfully.';
                $logMessage = 'User %s has received invitation and verified account';
                $user = $this->securityService->recover($token);
        }

        if ($user === null) {
            $this->addFlash(
                'danger',
                $this->translator->trans(
                    $failMessage,
                    [
                        '%url%' => $this->generateUrl('security_recovery'),
                    ]
                )
            );

            return $this->redirectToRoute('security_recovery');
        }

        $this->logger->info(sprintf($logMessage, $user->getEmail()));

        $this->addFlash(
            'success',
            $this->translator->trans($successMessage)
        );

        return $this->redirectToRoute($redirect);
    }

    /**
     * @Route("/user/password/recover", name="app_user_password_recover")
     * @IsGranted("ROLE_USER")
     */
    public function recover(Request $request, EntityManagerInterface $em): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if ($user === null) {
            $this->addFlash('warning', $this->translator->trans('Unable to find user session.'));

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

            $this->addFlash('success', $this->translator->trans('Password has been successfully changed.'));

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

            $this->addFlash('success', $this->translator->trans('New password has been set.'));

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
     * @Route("/user/email/change/{token}", name="security_email_change_token")
     * @IsGranted("ROLE_USER")
     */
    public function emailChange(Request $request, string $token, EntityManagerInterface $em): RedirectResponse
    {
        /** @var UserEmailChangeToken $token */
        $token = $em->getRepository(UserToken::class)->getToken($token, UserEmailChangeToken::class);

        if ($token === null || !$token->isValid(DateTimeService::getCurrentUTC())) {
            $this->addFlash(
                'danger',
                $this->translator->trans(
                    'Email change request is either invalid or expired. Please try requesting email change again.'
                )
            );

            return $this->redirectToRoute('user_profile_security');
        }

        /** @var User $user */
        $user = $this->getUser();
        $user->setEmail($token->getData());

        $em->remove($token);
        $em->flush();

        $this->addFlash(
            'success',
            $this->translator->trans(
                'You have successfully changed your email. From now on your email is %email%',
                [
                    '%email%' => $user->getEmail(),
                ]
            )
        );

        return $this->redirectToRoute('app_index');
    }
}
