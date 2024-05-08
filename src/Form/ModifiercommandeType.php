<?php

namespace App\Form;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;

use App\Entity\Commande;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
class ModifiercommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nom', TextType::class, [
            'constraints' => [
               
                new Assert\Type([
                    'type' => 'string',
                    'message' => 'Le nom doit être une chaîne de caractères.'
                ]),
            ],
        ])
        ->add('pre', TextType::class, [
            'constraints' => [
                new Assert\NotBlank(),
            ],
        ])
        ->add('mail', EmailType::class, [
            'constraints' => [
                new Assert\NotBlank(),
                new Assert\Email(),
            ],
        ])
        ->add('tel', TextType::class, [
            
        ])
        ->add('addr', TextType::class, [
            'constraints' => [
                new Assert\NotBlank(),
            ],
        ]);
}

    

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
        ]);
    }
}
