<?php

namespace App\Form;

use App\Entity\Album;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UploadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('image', FileType::class, [
            'label'    => 'Images',
            'multiple' => true,
            'required' => false,
            'mapped'   => false,
            'attr'     => [
                'accept' => 'image/*',
            ],
        ])
                ->add('album_name', EntityType::class, [
                    'class'       => Album::class,
                    'label'       => "Album",
                    'required'    => false,
                    'placeholder' => 'Sélectionner un album',
                ])
                ->add('new_album_name', TextType::class, [
                    'required' => false,
                    'label'    => 'Ou créer un nouvel album',
                ])
                ->add('date_taken', DateType::class, [
                    'label' => "Date",
                ])
                ->add('submit', SubmitType::class, [
                    'label' => 'Add Picture',
                ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
