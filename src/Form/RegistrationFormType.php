<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType as TypeTextType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;




class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
    
        $builder
    
        ->add('urlImage',FileType::class,[
            'required' => false,
            'mapped' => false,
        ])

        ->add('cin', IntegerType::class, [
            'attr' => ['class' => 'form-control'],
            'label' => 'CIN',
            'constraints' => [
                new NotBlank(['message' => 'Entrez votre CIN']),
            ],
        ])

        ->add('nom', TypeTextType::class, [
            'attr' => [
                'class' => 'form-control'
            ],
            'label' => 'Nom',
            'constraints' => [
                new NotBlank([
                    'message' => 'Entrer votre nom',
                ]),
            ],
        ])

        ->add('prenom', TypeTextType::class, [
            'attr' => [
                'class' => 'form-control'
            ],
            'label' => 'Prenom',
            'constraints' => [
                new NotBlank([
                    'message' => 'Entrer votre prenom',
                ]),
            ],
        ])
        ->add('username', TypeTextType::class, [
            'attr' => [
                'class' => 'form-control'
            ],
            'label' => 'username',
            'constraints' => [
                new NotBlank([
                    'message' => 'Please enter your phone username',
                ]),
            ],
        ])

        ->add('addEmail', EmailType::class, [
            'attr' => [
                'class' => 'form-control'
            ],   
            'label' => 'E-mail',
            'constraints' => [
                new Email([
                    'message' => 'The email "{{ value }}" is not a valid email address.'
                ]),
            ],
        ])
                  
        
            
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
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
            ])

            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'User' => 'ROLE_USER',
                    'Admin' => 'Admin',
                   
                ],
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'role-select', // Add an id to select element
                ],
                'label' => 'Roles',
                'multiple' => true,
                'expanded' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
