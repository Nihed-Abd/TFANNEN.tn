<?php

namespace App\Form;

use App\Entity\Design;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class DesignType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre')
            ->add('description')
            ->add('prix')
            ->add('categorie')
            ->add('picture', FileType::class, [
                'label' => 'Picture',
                'mapped' => false, // This tells Symfony not to try to map this field to an entity property
                'required' => false, // Set to false if the field is not required
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Design::class,
        ]);
    }
}
