<?php

namespace App\Form;

use App\Entity\Ad;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnnounceType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'title',
                TextType::class,
                $this->getConfiguration('Title', 'Add the best title ever')
            )
            ->add(
                'introduction',
                TextType::class,
                $this->getConfiguration('Introduction', 'Describe the ad')
            )
            ->add(
                'coverImage',
                UrlType::class,
                $this->getConfiguration('Cover image', 'Give awesome image url')
            )
            ->add(
                'content',
                TextareaType::class,
                $this->getConfiguration('Detailed description', 'Type a description that will make customers come to your place')
            )
            ->add(
                'rooms',
                IntegerType::class,
                $this->getConfiguration('Number of rooms', 'How many rooms are available ?')
            )
            ->add(
                'price',
                MoneyType::class,
                $this->getConfiguration('Price by night', 'Define the price per night', [
                    'currency' => 'USD',
                ])
            )
            ->add(
                'images',
                CollectionType::class,
                [
                    'entry_type' => ImageType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                ],
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ad::class,
        ]);
    }
}
