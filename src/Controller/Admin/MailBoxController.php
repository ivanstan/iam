<?php

namespace App\Controller\Admin;

use App\Entity\Mail;
use App\Repository\MailRepository;
use App\Repository\UserRepository;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/mailbox')]
final class MailBoxController extends AbstractController
{
    /**
     * @Route("/", name="admin_mailbox")
     * @Route("/{mail}", name="admin_mailbox_read")
     * @IsGranted("ROLE_ADMIN")
     */
    public function mailbox(Request $request, MailRepository $repository, UserRepository $userRepository, Mail $mail = null): Response
    {
        $query = $repository->findAll();
        $pager = new Pagerfanta(new DoctrineORMAdapter($query));
        $pager->setCurrentPage($request->query->get('page', 1));
        $pager->setMaxPerPage(8);

        if ($mail === null) {
            $iterator = $pager->getIterator();
            $mail = $iterator->getArrayCopy()[0] ?? null;
        }

        return $this->render(
            'admin/mailbox.html.twig',
            [
                'mail' => $mail,
                'pager' => $pager,
                'from' => $mail ? $userRepository->findByEmail($mail->getFrom())  : null,
                'to' => $mail ? $userRepository->findByEmail($mail->getTo()) : null,
            ]
        );
    }
}
