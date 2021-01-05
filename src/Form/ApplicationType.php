<?php

namespace App\Form;

use App\Entity\Application;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ApplicationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'Name',
                    ],
                ]
            )
            ->add(
                'url',
                UrlType::class,
                [
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'Url',
                    ],
                ]
            )
            ->add(
                'redirect',
                UrlType::class,
                [
                    'label' => false,
                    'attr' => [
                        'placeholder' => 'Redirect url',
                    ],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Application::class,
            ]
        );
    }
}
