<?php

namespace App\Controller\Admin;

use App\Form\AdminSettingsForm;
use App\Repository\SettingsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/admin")
 */
class SettingsController extends AbstractController
{
    /**
     * @Route("/settings", name="app_admin_settings")
     */
    public function users(Request $request, SettingsRepository $repository): Response
    {
        $form = $this->createForm(AdminSettingsForm::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $repository->set(SettingsRepository::REGISTRATION_ENABLED, $data[SettingsRepository::REGISTRATION_ENABLED] ?? true);
        }

        return $this->render('admin/settings.html.twig', ['form' => $form->createView()]);
    }
}
