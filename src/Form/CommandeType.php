<?php

namespace App\Form;

use App\Entity\Commande;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType; // Use DateType instead of TextType
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Validator\Constraints as Assert; // Add this line
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;



class CommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('adresse', TextType::class, [
            'label' => 'Address',
             ])
             ->add('num_tel', TextType::class, [
                'label' => 'Phone Number',
            ])
            ->add('prix', NumberType::class, [
                'label' => 'Price', // Set the readonly attribute for prix
            ])
            ->add('produits', null, [
                'attr' => ['readonly' => true], // Set the readonly attribute for produits
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
        ]);
    }
}
