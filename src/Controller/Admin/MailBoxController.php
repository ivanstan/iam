<?php

namespace App\Controller\Admin;

use App\Entity\Mail;
use App\Repository\MailRepository;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/admin")
 */
final class MailBoxController extends AbstractController
{
    /**
     * @Route("/mailbox", name="admin_mailbox")
     * @Route("/mailbox/{mail}", name="admin_mailbox_read")
     * @IsGranted("ROLE_ADMIN")
     */
    public function mailbox(Request $request, MailRepository $repository, Mail $mail = null): Response
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
            ]
        );
    }
}
