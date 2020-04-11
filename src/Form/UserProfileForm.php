<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserProfileForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'firstName',
                TextType::class,
                [
                    'label' => false,
                    'attr' => ['placeholder' => 'First name']
                ]
            )
            ->add(
                'lastName',
                TextType::class,
                [
                    'label' => false,
                    'attr' => ['placeholder' => 'Last name']
                ]
            )
            ->add(
                'preference',
                UserPreferenceForm::class,
                [
                    'label' => false,
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => User::class,
            ]
        );
    }
}
