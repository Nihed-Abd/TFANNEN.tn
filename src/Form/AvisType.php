<?php

namespace App\Form;

use App\Entity\Avis;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType; // Import TextType for the commentaire field
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface; // Import ExecutionContextInterface for callback validation

class AvisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('avis', null, [
            'constraints' => [
                new NotBlank(['message' => 'Avis is required']), 
            ],
        ])
        ->add('commentaire', TextType::class, [ // Change to TextType for string fields
            'constraints' => [
                new NotBlank(['message' => 'Commentaire is required']),
                new Length([
                    'min' => 1,
                    'minMessage' => 'Commentaire must be at least {{ limit }} characters long',
                ]),
                new Callback([$this, 'validateCommentaire']),
            ],
        ])
        ->add('design_id', HiddenType::class, [
            'mapped' => false, 
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Avis::class,
        ]);
    }

    public function validateCommentaire($value, ExecutionContextInterface $context)
    {
        $badWords = ['fuck', 'shit'];

        foreach ($badWords as $word) {
            if (stripos($value, $word) !== false) {
                $context->buildViolation('The commentaire contains inappropriate language')->addViolation();
                break; // Exit loop if a bad word is found
            }
        }
    }
}
