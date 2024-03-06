<?php
namespace App\Form;

use App\Entity\Promotion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;


class PromotionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('taux_reduction')
         ->add('type_promo', ChoiceType::class, [
    'choices' => [
        'Promo générale' => 'promo_generale',
        'Code promo' => 'code_promo', 
        /*'Promotion mbr d\'utilisateurs' => 'min_utilisateur',
        'Promotionmin montant d\'achat ' => 'min_montant',*/
    ],
    'label' => 'Type de promotion',
    'multiple' => false,
    'expanded' => true,
    'attr' => ['class' => 'promo-type'],
    'choice_attr' => function($choice, $key, $value) {
        return ['style' => 'display: block; margin-bottom: 5px;margin-right: 10px;margin-left: 10px;'];
    }
])
           
            
            ->add('code_promo')
          //  ->add('min_utilisateur')
           // ->add('min_montant')
            ->add('date_debut')
            ->add('date_fin');

      
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Promotion::class,
        ]);
    }
}