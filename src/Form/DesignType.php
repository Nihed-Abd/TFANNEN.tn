<?php

namespace App\Form;

use App\Entity\Design;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

class DesignType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', null, [
                'constraints' => [
                    new NotBlank(['message' => 'Title cannot be blank']), 
                    new Length([
                        'min' => 3,
                        'minMessage' => 'Title must be at least {{ limit }} characters long',
                    ]), 
                ],
            ])
            ->add('description', null, [
                'constraints' => [
                    new NotBlank(['message' => 'Description cannot be blank']), 
                ],
            ])
            ->add('prix', null, [
                'constraints' => [
                    new NotBlank(['message' => 'Price cannot be blank']), 
                ],
            ])
            ->add('categorie', ChoiceType::class, [
                'label' => 'Category',
                'choices' => [
                    'T-shirts' => 't_shirts',
                    'Hoodies' => 'hoodies',
                    'Pants' => 'pants',
                    'Dresses' => 'dresses',
                    'Jackets' => 'jackets',
                    'Sweatshirts' => 'sweatshirts',
                    'Skirts' => 'skirts',
                    'Sports Themes' => 'sports_Themes',
                    'Funniest and Troll' => 'Funny',
                    'Hats' => 'hat',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Category cannot be blank']), 
                ],
            ])
            ->add('picture', FileType::class, [
                'label' => 'Picture',
                'mapped' => false, 
                'required' => true, 
                'constraints' => [
                    new NotBlank(['message' => 'Please upload a picture']),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Design::class,
        ]);
    }
}
