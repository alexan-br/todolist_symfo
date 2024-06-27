<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Project;
use App\Entity\Task;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('status', ChoiceType::class, [
                'label' => 'État',
                'choices' => [
                    'Pas commencée' => 'not_started',
                    'En cours' => 'in_progress',
                    'Terminée' => 'completed',
                ],
            ]);
        // ->add('project', EntityType::class, [
        //     'class' => Project::class,
        //     'choice_label' => 'id',
        // ])
        // ->add('categories', EntityType::class, [
        //     'class' => Category::class,
        //     'choice_label' => 'id',
        //     'multiple' => true,
        // ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}
