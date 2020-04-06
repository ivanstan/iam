<?php

namespace App\Form;

use App\Repository\SettingsRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

class AdminSettingsForm extends AbstractType
{
    protected SettingsRepository $repository;

    public function __construct(SettingsRepository $repository)
    {
        $this->repository = $repository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            SettingsRepository::REGISTRATION_ENABLED,
            CheckboxType::class,
            [
                'data' => $this->repository->isRegistrationEnabled(),
                'required' => false,
                'attr' => [
                    'data-test' => 'feature-registration',
                ],
            ]
        );
    }
}
