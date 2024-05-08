<?php

namespace App\Form;

use App\Entity\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class ClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Last Name:',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter your last name',
                ],
            ])
            ->add('prenom', TextType::class, [
                'label' => 'First Name:',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter your first name',
                ],
            ])
            ->add('age', NumberType::class, [
                'label' => 'Age:',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter your age',
                ],
            ])
            ->add('poids', NumberType::class, [
                'label' => 'Weight (kg):',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter your weight',
                ],
            ])
            ->add('hauteur', NumberType::class, [
                'label' => 'Height (cm):',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter your height',
                ],
            ])
          
            
            ->add('submit', SubmitType::class, [
                'label' => 'Submit',
                'attr' => [
                    'class' => 'btn btn-primary btn-lg mb-3', 
                    'style' => 'padding-left: 30px; padding-right: 33px;', 
                ],
            ]) ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
        ]);
    }
}
