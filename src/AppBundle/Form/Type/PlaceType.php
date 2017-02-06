<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Place;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class PlaceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class, ['description' => "place name"]);
        $builder->add('address', TextType::class, ['description' => "complete address of place"]);
        $builder->add('prices', CollectionType::class, [
            'entry_type' => PriceType::class,
            'allow_add' => true,
            'error_bubbling' => true,
            'description' => 'List of prices fot this place'
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Place::class,
            'csrf_protection' =>  false
        ]);
    }
}