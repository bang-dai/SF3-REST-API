<?php

namespace AppBundle\Form\Validator\Constraint;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;


class PriceTypeUniqueValidator extends ConstraintValidator
{
    public function validate($prices, Constraint $constraint)
    {
        if(!($prices instanceof ArrayCollection)){
            return;
        }

        $pricesType = [];
        foreach ($prices as $price){
            if(in_array($price->getType(), $pricesType)){
                $this->context->buildViolation($constraint->message)->addViolation();
            }else{
                $pricesType[] = $price->getType();
            }
        }
    }
}