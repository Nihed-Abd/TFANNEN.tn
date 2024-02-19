<?php

namespace App\Form;

use App\Entity\Reclamation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ReclamationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('objet')
            ->add('type_de_reclamation')
            ->add('description_reclamation')
            ->add('date', DateTimeType::class, [
                'widget' => 'single_text',
                // Optional: set the default value to the current date and time
                'data' => new \DateTime(),
                // Optional: disable the date input to prevent users from modifying it
                'disabled' => true,
            ])
            ->add('User')
        ;

        // Add an event listener to set the date field with the current date
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();
            $data = $event->getData();

            // If creating a new Reclamation (data is null), set the date field with the current date
            if (!$data || null === $data->getDate()) {
                $form->get('date')->setData(new \DateTime());
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reclamation::class,
        ]);
    }
}
