<?php

namespace Furniture\PricingBundle\Calculator;

use Furniture\PricingBundle\Model\PricingInterface;
use Sylius\Component\User\Model\UserInterface;

class UserBasedPriceCalculator 
{
    
    public function calculateByUserRules(PricingInterface $object, UserInterface $user, array $context = []){
        return $object->getPrice();
    }
    
}

