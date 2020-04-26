<?php

namespace App\Form;

use App\Entity\Booking;
use App\Form\DataTransformer\StringToDateTimeTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookingType extends ApplicationType
{
    private StringToDateTimeTransformer $transformer;

    public function __construct(StringToDateTimeTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'startDate',
                TextType::class,
                $this->getConfiguration('From', 'Enter the arrival date'),
            )
            ->add(
                'endDate',
                TextType::class,
                $this->getConfiguration('To', 'Enter the departure date'),
            )
            ->add(
                'comment',
                TextareaType::class,
                $this->getConfiguration('Comment', 'If you have any wish let us know about it...', ['required' => false]),
            );

        $builder->get('startDate')->addModelTransformer($this->transformer);
        $builder->get('endDate')->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }
}
