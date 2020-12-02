<?php

namespace App\Form;

use App\Entity\User;
use App\Security\Role;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints\Email;

class UserEditForm extends AbstractType
{
    private ?UserInterface $user;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        if ($tokenStorage->getToken()) {
            $this->user = $tokenStorage->getToken()->getUser();
        }
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $user = $event->getData();
                $form = $event->getForm();
                $isNew = !$user || $user->getId() === null;

                $form->add(
                    'email',
                    EmailType::class,
                    [
                        'constraints' => [new Email()],
                        'label' => false,
                        'disabled' => !$isNew,
                        'attr' => [
                            'placeholder' => 'Email',
                            'data-test' => 'email',
                        ],
                    ]
                );

                if ($isNew) {
                    $form->add(
                        'plainPassword',
                        PasswordRepeatType::class,
                        [
                            'label' => false,
                            'required' => false,
                        ]
                    );
                }

                $form->add(
                    'roles',
                    ChoiceType::class,
                    [
                        'choices' => Role::toArray(),
                        'expanded' => true,
                        'multiple' => true,
                        'choice_attr' => static function ($key) {
                            return $key === Role::USER ? ['disabled' => 'disabled'] : [];
                        },
                    ]
                );

                $activeOptions = [
                    'required' => false,
                    'attr' => [
                        'data-test' => 'user-active',
                    ],
                    'data' => $isNew,
                    'disabled' => $user->getId() === $this->user->getId(),
                ];

                if ($isNew) {
                    $activeOptions['data'] = true;
                }

                $form->add('active', CheckboxType::class, $activeOptions);


                $verifiedOptions = [
                    'required' => false,
                    'attr' => [
                        'data-test' => 'user-verified',
                    ],
                    'data' => $isNew,
                ];

                if ($isNew) {
                    $verifiedOptions['data'] = true;
                }

                $form->add('verified', CheckboxType::class, $verifiedOptions);

                $form->add('banned', CheckboxType::class, [
                    'required' => false,
                    'disabled' => $user->getId() === $this->user->getId(),
                    'attr' => [
                        'data-test' => 'user-banned',
                    ],
                ]);

                if ($isNew) {
                    $form->add(
                        'invite',
                        CheckboxType::class,
                        [
                            'required' => false,
                            'mapped' => false,
                            'label' => 'Send invitation',
                            'attr' => [
                                'data-test' => 'user-invite',
                            ],
                        ]
                    );
                }
            }
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
