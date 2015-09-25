<?php

namespace Furniture\PricingBundle\Twig;

use Furniture\PricingBundle\Model\PricingInterface;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Templating\Helper\Helper;
use Sylius\Component\Currency\Context\CurrencyContextInterface;

class PricingExtension extends \Twig_Extension {
    
    protected $calculator;
    
    protected $authorizedUser;
    
    protected $currencyHelper;
    
    protected $moneyHelper;
    
    protected $currencyContext;
            
    function __construct($calculator, TokenStorageInterface $tokenStorage, CurrencyContextInterface $currencyContext, $currencyHelper, $moneyHelper) {
        
        $this->currencyContext = $currencyContext;
        
        $this->calculator = $calculator;
        
        $this->moneyHelper = $moneyHelper;
        
        $this->currencyHelper = $currencyHelper;
        
        if($token = $tokenStorage->getToken()){
            $this->authorizedUser = $token->getUser();
        }
    }
    
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('userPrice', array($this, 'userPriceFilter')),
        );
    }
    
    public function userPriceFilter(PricingInterface $object, array $context = []){
        if($this->authorizedUser){
            $currency = $this->currencyContext->getCurrency();
            $calculatedPrice = $this->calculator->calculateByUserRules($object, $this->authorizedUser, $context);
            return $this->moneyHelper->formatAmount($calculatedPrice, $currency);
        }
        return 0;
    }
    
    public function getName()
    {
        return 'pricing_extension';
    }
    
}

