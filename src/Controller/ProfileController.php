<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserForm;
use App\Service\Traits\TranslatorAwareTrait;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ProfileController extends AbstractController
{
    use TranslatorAwareTrait;

    /**
     * @Route("/user/profile", name="user_profile_edit")
     * @IsGranted("ROLE_USER")
     */
    public function profile(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $preference = $user->getPreference();
        if (!$preference->getId()) {
            $preference->setTimezone($this->getParameter('default_timezone'));
        }

        $form = $this->createForm(UserForm::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if (!$preference->getId()) {
                $user->setPreference($preference);
                $em->persist($preference);
            }

            $em->flush();

            return $this->redirectToRoute('user_profile_edit');
        }

        return $this->render(
            'pages/user/profile.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/user/account", name="user_profile_security")
     * @IsGranted("ROLE_USER")
     */
    public function account(): Response
    {
        return $this->render('pages/user/account.html.twig',);
    }

}
