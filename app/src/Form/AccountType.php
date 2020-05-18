<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AccountType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'firstName',
                TextType::class,
                $this->getConfiguration('First name', 'Your First name...'),
            )
            ->add(
                'lastName',
                TextType::class,
                $this->getConfiguration('Last name', 'Your Last name...'),
            )
            ->add(
                'email',
                EmailType::class,
                $this->getConfiguration('Email', 'Your email address...'),
            )
            ->add(
                'picture',
                UrlType::class,
                $this->getConfiguration('Profile picture', 'Your Url profile picture...'),
            )
            ->add(
                'introduction',
                TextType::class,
                $this->getConfiguration('Introduction', 'Introduce yourself...'),
            )
            ->add(
                'description',
                TextareaType::class,
                $this->getConfiguration('Description', 'Describe yourself...'),
            )
        ;
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
