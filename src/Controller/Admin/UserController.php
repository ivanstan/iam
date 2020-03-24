<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserAdminType;
use App\Repository\UserRepository;
use App\Security\SecurityMailerService;
use App\Service\Traits\TranslatorAwareTrait;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/admin")
 */
class UserController extends AbstractController
{
    use TranslatorAwareTrait;

    /**
     * @Route("/users", name="app_admin_users")
     */
    public function users(Request $request, UserRepository $repository): Response
    {
        $query = $repository->search($request->query->get('search', ''));
        $pager = new Pagerfanta(new DoctrineORMAdapter($query));
        $pager->setCurrentPage($request->query->get('page', 1));

        return $this->render(
            'admin/users.html.twig',
            [
                'pager' => $pager,
            ]
        );
    }

    /**
     * @Route("/user/new", name="user_new", methods={"GET","POST"})
     * @Route("/user/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user = null): Response
    {
        if ($user === null) {
            $user = new User();
        }

        $form = $this->createForm(UserAdminType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if ($user->getId() === null) {
                $em->persist($user);
            }

            $em->flush();

            if (isset($form['invite']) && $form['invite']->getData()) {
                try {
//                    $recovery->invite($user);
                } catch (\Exception $e) {
                    $this->addFlash('danger', $this->translator->trans('misc.messages.email_fail'));
                }
            }

            return $this->redirectToRoute('app_admin_users');
        }

        return $this->render('pages/admin/user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/user/{id}/delete", name="user_delete", methods={"DELETE"})
     */
    public function delete(Request $request, User $user): Response
    {
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        if ($currentUser->getId() === $user->getId()) {
            $this->addFlash('warning', $this->translator->trans('Unable to delete yourself.'));

            return $this->redirectToRoute('app_admin_users');
        }

        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();
        }

        return $this->redirectToRoute('app_admin_users');
    }
}
