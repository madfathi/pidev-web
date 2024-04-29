<?php

namespace App\Form;

use App\Entity\Seance;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;
class SeanceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('typeSeance')
            ->add('dureeSeance')
            ->add('nbMaximal')
            ->add('categorie',FileType::class,[
                'label' => 'categorie',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        
                        'mimeTypesMessage' => 'Please upload a valid image file',
                    ])
                ],])
            ->add('dateFin')
        
          
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Seance::class,
        ]);
    }
}
