<?php

namespace App\Controller\Admin;

use App\Entity\Application;
use App\Form\ApplicationType;
use App\Repository\ApplicationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

#[Route("/admin/application")]
class ApplicationController extends AbstractController
{
    #[Route('/', name: 'admin_application_index', methods: ['GET'])]
    public function index(
        ApplicationRepository $applicationRepository
    ): Response {
        return $this->render(
            'admin/application/application.html.twig',
            [
                'applications' => $applicationRepository->findAll(),
            ]
        );
    }

    #[Route('/new', name: 'admin_application_new', methods: ['GET', 'POST'])]
    #[Route('/{id}/edit', name: 'admin_application_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        ?Application $application
    ): Response {
        $application = $application ?? new Application();

        $form = $this->createForm(ApplicationType::class, $application);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($application->getId() === null) {
                $application->setUuid(Uuid::v4());
                $this->getDoctrine()->getManager()->persist($application);
            }

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_application_index');
        }

        return $this->render('admin/application/edit.html.twig', [
            'application' => $application,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'admin_application_delete', methods: ['DELETE'])]
    public function delete(Request $request, Application $application): Response
    {
        if ($this->isCsrfTokenValid('delete_application'.$application->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($application);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_application_index');
    }
}
