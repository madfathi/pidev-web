<?php

namespace App\Form;

use App\Entity\User;
use Doctrine\DBAL\Types\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType as TypeTextType;
use Symfony\Component\Validator\Constraints\Email;


class ProfileFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

        ->add('urlImage',FileType::class,[
            'required' => false,
            'mapped' => false,
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

        ->add('numTlfn')
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
        ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
