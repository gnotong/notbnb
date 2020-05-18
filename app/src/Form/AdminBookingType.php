<?php

namespace App\Form;

use App\Entity\Ad;
use App\Entity\Booking;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminBookingType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'startDate',
                DateType::class,
                $this->getConfiguration('From', 'Format: yyyy/mm/dd', [
                    'widget' => 'single_text'
                ]),
            )
            ->add(
                'endDate',
                DateType::class,
                $this->getConfiguration('To', 'Format: yyyy/mm/dd', [
                    'widget' => 'single_text'
            ]))
            ->add('comment')
            ->add(
                'booker',
                EntityType::class, [
                    'disabled' => true,
                    'class' => User::class,
                    'choice_label' => fn(User $user) => $user->getFirstName() . ' ' . strtoupper($user->getLastName())
                ]
            )
            ->add(
                'ad',
                EntityType::class, [
                    'class' => Ad::class,
                    'choice_label' => 'title',
                ],
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }
}
