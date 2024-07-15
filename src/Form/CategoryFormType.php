<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Post;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('body')
            ->add('status', ChoiceType::class, [
                'choices'  => [
                    'Active' => 1,
                    'Inactive' => 0
                ],
                'expanded' => true,  // This makes the choices appear as radio buttons
                'multiple' => false, // This ensures that it's a single-choice (radio buttons)
                'choice_attr' => function($choice, $key, $value) {
                    // Add a class to each radio button
                    return ['class' => 'form-check-input'];
                },
            ])
            ->add('url')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}
