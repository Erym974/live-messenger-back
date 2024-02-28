<?php

namespace App\Form;

use App\Entity\Job;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JobType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('category', ChoiceType::class, [
                'label' => 'Category',
                'choices' => [
                    'Machine Learning' => 'Machine Learning',
                    'Data Platform Engineering' => 'Data Platform Engineering',
                    'Activities Platform' => 'Activities Platform',
                    'Communications & PR' => 'Communications & PR',
                    'Core Tech Engineering' => 'Core Tech Engineering',
                    'Product Management' => 'Product Management',
                    'Designer' => 'Designer',
                    'Developer' => 'Developer',
                ],
            ])
            ->add('location', ChoiceType::class, [
                'label' => 'Location',
                'choices' => [
                    'Remote' => 'Remote',
                    'On-site' => 'On-site',
                    'Hybrid' => 'Hybrid',
                ],
            ])
            ->add('short_description')
            ->add('long_description')
            ->add('requirements', CollectionType::class, [
                'entry_type' => TextType::class,
                'delete_empty' => true,
                'allow_add' => true,
                'prototype' => true,
                'entry_options' => [
                    'label' => false
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Job::class,
        ]);
    }
}
