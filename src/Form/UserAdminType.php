<?php

namespace App\Form;

use App\Entity\User;
use App\Service\Security\Role;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;

class UserAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, static function (FormEvent $event) {
            $user = $event->getData();
            $form = $event->getForm();
            $isNew = !$user || null === $user->getId();

            $form->add('email', EmailType::class, [
                'constraints' => [new Email()],
                'label' => false,
                'disabled' => !$isNew,
                'attr' => ['placeholder' => 'Email'],
            ]);

            if ($isNew) {
                $form->add('plainPassword', PasswordRepeatType::class, ['label' => false, 'required' => false]);
            }

            $form->add('roles', ChoiceType::class, [
                'choices' => Role::toArray(),
                'expanded' => true,
                'multiple' => true,
                'choice_attr' => static function ($key) {
                    return $key === Role::USER ? ['disabled' => 'disabled'] : [];
                },
            ]);

            $options = ['required' => false];
            if ($isNew) {
                $options['data'] = true;
            }

            $form->add('active', CheckboxType::class, $options);
            $form->add('verified', CheckboxType::class, $options);

            if ($isNew) {
                $form->add('invite', CheckboxType::class, [
                    'required' => false,
                    'mapped' => false,
                    'label' => 'Send invitation',
                ]);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
