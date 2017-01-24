<?php
/**
 * Created by PhpStorm.
 * User: bdai
 * Date: 24/01/2017
 * Time: 18:05
 */

namespace AppBundle\Form\Type;


use AppBundle\Entity\Price;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PriceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('type');
        $builder->add('value');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Price::class,
            'csrf_protection' => false
        ]);
    }

}