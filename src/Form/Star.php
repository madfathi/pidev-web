<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Star extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => [
                '1-Star ' => 1,
                ' 2-Stars ' => 2,
                '3-Stars ' => 3,
                '4-Stars ' => 4,
                '5-Stars ' => 5,
            ],
            'expanded' => true,
            'multiple' => false,
            'label' => false,
            'attr' => ['class' => 'star-rating'],
        ]);
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}
