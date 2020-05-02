<?php

namespace App\Form;

use App\Entity\Comment;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'content',
                TextareaType::class,
                $this->getConfiguration('Your feedback', 'Please ad your feedback here. It will help us to improve our services !'),
            )
            ->add(
                'rating',
                IntegerType::class,
                $this->getConfiguration('Grade out of 5', 'Grade from 1 to 5', [
                    'attr' => [
                        'min' => 0,
                        'max' => 5,
                        'step' => 1,
                    ]
                ]),
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
