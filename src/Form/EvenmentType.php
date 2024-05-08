<?php

namespace App\Form;

use App\Entity\Evenment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Form\Extension\Core\Type\DateType;



class EvenmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nomEvent', TextType::class, [
            'constraints' => [
                new Regex([
                    'pattern' => '/^[a-zA-Z\s]+$/',
                    'message' => 'Le nom de l\'événement ne peut contenir que des lettres et des espaces.',
                ]),
            ],
        ])
        ->add('dateEvent', DateType::class, [
            'constraints' => [
                new GreaterThanOrEqual([
                    'value' => new \DateTime(), // Current system date
                    'message' => 'La date de l\'événement doit être supérieure ou égale à la date actuelle.',
                ]),
            ],
        ])
            ->add('lieuEvent', TextType::class, [
                'constraints' => [
                    new Regex([
                        'pattern' => '/^[a-zA-Z\s]+$/',
                        'message' => 'Le lieu de l\'événement ne peut contenir que des lettres et des espaces.',
                    ]),
                ],
            ])
            ->add('nomStar', TextType::class, [
                'constraints' => [
                    new Regex([
                        'pattern' => '/^[a-zA-Z\s]+$/',
                        'message' => 'Le nom de la star ne peut contenir que des lettres et des espaces.',
                    ]),
                ],
            ])
            ->add('image', FileType::class, [
                'mapped' => false,
                'required' => false,
            ])
            ->add('button', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Evenment::class,
        ]);
    }
}
