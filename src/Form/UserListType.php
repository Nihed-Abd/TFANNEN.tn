<?php

namespace App\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class UserListType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // Search input field
            ->add('search', SearchType::class, [
                'required' => false,
                'label' => false,
                'attr' => [
                    'class' => 'form-control bg-dark border-0 dynamic-search',
                    'placeholder' => 'Search',
                ],
            ])
            // Sorting select input field
            ->add('sortBy', ChoiceType::class, [
                'required' => false,
                'label' => false,
                'choices' => [
                    'Email' => 'email',
                    'Username' => 'username',
                ],
                'attr' => [
                    'class' => 'form-control dynamic-sort',
                ],
            ])
        ;
    }
}