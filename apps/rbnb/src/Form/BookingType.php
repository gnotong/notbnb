<?php

namespace App\Form;

use App\Entity\Booking;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookingType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'startDate',
                DateType::class,
                $this->getConfiguration('From', 'Enter the arrival date', ['widget' => 'single_text']),
            )
            ->add(
                'endDate',
                DateType::class,
                $this->getConfiguration('To', 'Enter the departure date', ['widget' => 'single_text']),
            )
            ->add(
                'comment',
                TextareaType::class,
                $this->getConfiguration('Comment', 'If you have any wish let us know about it...', ['required' => false]),
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }
}
