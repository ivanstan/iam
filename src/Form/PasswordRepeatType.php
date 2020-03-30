<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class PasswordRepeatType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('plainPassword', RepeatedType::class, [
            'type' => PasswordType::class,
            'invalid_message' => 'Password must match',
            'required' => true,
            'first_options' => [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Password',
                    'data-test' => 'password',
                ],
            ],
            'second_options' => [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Repeat password',
                    'data-test' => 'repeat-password',
                ],
            ],
            'constraints' => [
                new NotBlank([
                    'groups' => 'profile_password',
                    'message' => 'Password must not be blank',
                ]),
                new Length([
                    'min' => 6,
                    'minMessage' => 'Minimum password length is six characters.',
                    'max' => 4096,
                ]),
            ],

        ])->addModelTransformer(new CallbackTransformer(
            static function ($passwordAsArray) {
                return $passwordAsArray;
            },
            static function ($passwordAsString) {

                if ($passwordAsString instanceof User) {
                    return $passwordAsString->getPlainPassword();
                }

                return $passwordAsString['plainPassword'] ?? $passwordAsString;
            }
        ));
    }
}
