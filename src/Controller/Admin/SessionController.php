<?php

namespace App\Controller\Admin;

use App\Entity\Session;
use App\Model\Api\CollectionSpecification;
use App\Repository\SessionRepository;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */
final class SessionController extends AbstractController
{
    /**
     * @Route("/sessions", name="admin_session_index")
     */
    public function index(Request $request, SessionRepository $repository): Response
    {
        $builder = $repository->collection(
            (new CollectionSpecification())
                ->setQuery(
                    $request->query->get('query')
                )
                ->setSort(
                    $request->query->get('sort', 'ip')
                )
                ->setSortDirection(
                    $request->query->get('sort-dir')
                )
        );

        $pager = new Pagerfanta(new DoctrineORMAdapter($builder));
        $pager->setCurrentPage($request->get('page', 1));

        return $this->render(
            'admin/session.html.twig',
            [
                'pager' => $pager,
            ]
        );
    }

    /**
     * @Route("/session/{id}/delete", name="admin_session_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Session $session): Response
    {
        if ($this->isCsrfTokenValid('delete' . $session->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($session);
            $em->flush();
        }

        return $this->redirectToRoute('admin_session_index');
    }
}
