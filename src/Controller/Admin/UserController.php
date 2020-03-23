<?php

namespace App\Controller\Admin;

use App\Repository\UserRepository;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/admin")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/users", name="app_admin_users")
     */
    public function users(UserRepository $repository): Response
    {
        return $this->render(
            'admin/users.html.twig',
            [
                'pager' => new Pagerfanta(new ArrayAdapter($repository->findAll())),
            ]
        );
    }
}
