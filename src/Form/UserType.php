<?php

/**
 * UserType - This class is responsible for creating the form for the User entity.]
 **/
declare(strict_types=1);

namespace App\Form;

use App\Document\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * UserType
 * ---------------------
 * This class is responsible for creating the form for the User entity.
 **/
class UserType extends AbstractType
{
    /**
     *  buildForm -  This function is responsible for building the form for the User entity.
     *
     * @param  FormBuilderInterface  $builder  - The form builder interface
     * @param  array  $options  - The options for the form
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => false,
                'attr' => [
                    'autocomplete' => 'firstName',
                    'class' => 'bg-transparent block mt-10 mx-auto border-b-2 w-1/5 h-20 text-2xl outline-none',
                    'placeholder' => 'First Name'
                ],
            ])
            ->add('lastName', TextType::class, [
                'label' => false,
                'attr' => [
                    'autocomplete' => 'lastName',
                    'class' => 'bg-transparent block mt-10 mx-auto border-b-2 w-1/5 h-20 text-2xl outline-none',
                    'placeholder' => 'Last Name'
                ],
            ])
            ->add('email', TextType::class, [
                'label' => false,
                'attr' => [
                    'autocomplete' => 'email',
                    'class' => 'bg-transparent block mt-10 mx-auto border-b-2 w-1/5 h-20 text-2xl outline-none',
                    'placeholder' => 'Email'
                ],
            ])
            ->add('password', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'label' => false,
//                'mapped' => false,
                'attr' => [
                    'autocomplete' => 'new-password',
                    'class' => 'bg-transparent block mt-10 mx-auto border-b-2 w-1/5 h-20 text-2xl outline-none',
                    'placeholder' => 'Password'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ]);
    }

    /**
     *  configureOptions - This function is responsible for setting the default options for the form.
     *
     * @param  OptionsResolver  $resolver  - The options resolver
     * */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => User::class,
            ]
        );
    }
}