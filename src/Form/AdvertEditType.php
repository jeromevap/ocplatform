<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AdvertEditType extends AbstractType
{

    /**
     * Héritage de formulaire
     * @return string
     */
    public function getParent()
    {
        return AdvertType::class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->remove('date');
    }
}
