<?php

namespace App\Form;

use App\Entity\Ad;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnnounceType extends AbstractType
{

    /**
     * Helps define placeholders and fields labels
     * @param string $label
     * @param string $placeholder
     * @return array
     */
    private function getConfiguration(string $label, string $placeholder): array
    {
        return [
            'label' => $label,
            'attr' => [
                'placeholder' => $placeholder,
            ],
        ];
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, $this->getConfiguration('Title', 'Add the best title ever'))
            ->add('introduction', TextType::class, $this->getConfiguration('Introduction', 'Describe the ad'))
            ->add('coverImage', UrlType::class, $this->getConfiguration('Cover image', 'Give awesome image url'))
            ->add('content', TextareaType::class, $this->getConfiguration('Detailed description', 'Type a description that will make customers come to your place'))
            ->add('rooms', IntegerType::class, $this->getConfiguration('Number of rooms', 'How many rooms are available ?'))
            ->add('price', MoneyType::class, $this->getConfiguration('Price by night', 'Define the price per night'))
            ->add('save', SubmitType::class, [
                'label' => 'Create new ad',
                'attr' => [
                    'class' => 'btn btn-primary',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ad::class,
        ]);
    }
}
