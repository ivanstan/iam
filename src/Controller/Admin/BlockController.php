<?php

namespace App\Controller\Admin;

use App\Entity\Lock;
use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */
final class BlockController extends AbstractController
{
    /**
     * @Route("/block", name="block_index")
     */
    public function index(Request $request): Response
    {
        /** @var QueryBuilder $builder */
        $builder = $this->getDoctrine()->getRepository(Lock::class)->findAll();

        $pager = new Pagerfanta(new DoctrineORMAdapter($builder));
        $pager->setCurrentPage($request->get('page', 1));

        return $this->render('admin/block.html.twig', [
            'pager' => $pager,
        ]);
    }

    /**
     * @Route("/block/{id}/delete", name="block_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Lock $lock): Response
    {
        if ($this->isCsrfTokenValid('delete'.$lock->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($lock);
            $em->flush();
        }

        return $this->redirectToRoute('block_index');
    }
}
