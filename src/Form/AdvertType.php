<?php

namespace App\Form;

use App\Entity\Advert;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdvertType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
          'date',
          DateTimeType::class,
          ['date_widget' => 'single_text', 'time_widget' => 'single_text']
        )
          ->add('title', TextType::class)
          ->add('content', TextareaType::class)
          ->add('author', TextType::class)
          ->add('published', CheckboxType::class, ['required' => false])
          ->add('image', FileType::class)
          /*
           * Rappel :
           * - 1er argument : nom du champ, ici « categories », car c'est le nom de l'attribut
           * - 2e argument : type du champ, ici « CollectionType » qui est une liste de quelque chose
           * - 3e argument : tableau d'options du champ
           */
          ->add(
            'categories',
            EntityType::class,
            [
              'class' => Category::class,
              'choice_label' => 'name',
              'multiple' => true,
            ]
          )
          ->add('save', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
          [
            'data_class' => Advert::class,
          ]
        );
    }

}
