<?php

namespace App\Form;

use App\Repository\SettingsRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class AdminSettingsForm extends AbstractType
{
    protected SettingsRepository $repository;

    public function __construct(SettingsRepository $repository)
    {
        $this->repository = $repository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(SettingsRepository::REGISTRATION_ENABLED, CheckboxType::class)
            ->setRequired(false);

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $event->getForm()->get(SettingsRepository::REGISTRATION_ENABLED)->setData(
                    $this->repository->isRegistrationEnabled()
                );
            }
        );
    }
}
