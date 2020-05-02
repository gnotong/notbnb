<?php

namespace App\Form;

use App\Entity\PasswordReset;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PasswordResetType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'old',
                PasswordType::class,
                $this->getConfiguration('Old password', 'Enter old password'),
            )
            ->add(
                'new',
                PasswordType::class,
                $this->getConfiguration('New password', 'Enter new password'),
            )
            ->add(
                'confirm',
                PasswordType::class,
                $this->getConfiguration('Confirm password', 'Enter confirm password'),
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PasswordReset::class
        ]);
    }
}
