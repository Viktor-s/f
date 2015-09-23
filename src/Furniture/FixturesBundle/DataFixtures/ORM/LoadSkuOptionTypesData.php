<?php

namespace Furniture\FixturesBundle\DataFixtures\ORM;

use Sylius\Bundle\FixturesBundle\DataFixtures\DataFixture;
use \Doctrine\Common\Persistence\ObjectManager;

class LoadSkuOptionTypesData extends DataFixture {
    
    public function getOrder() {
        return 19;
    }
    
    public function load(ObjectManager $manager) {
        
        $skuOptionType = $this->createSkuOptionType([
            $this->defaultLocale => [
                'name' => 'Size'
                ]
        ]);
        $manager->persist($skuOptionType);
        
        $manager->flush();
    }
    
    public function createSkuOptionType(array $translations){
        /* @var $skuOptionType Furniture\SkuOptionBundle\Entity\SkuOptionType */
        $skuOptionType = $this->get('Furniture.repository.sku_option')->createNew();
        
        foreach ($translations as $locale => $presentation) {
            $skuOptionType->setCurrentLocale($locale);
            $skuOptionType->setName($presentation['name']);
        }
        
        $this->setReference('Furniture.factory.'.$skuOptionType->getName(), $skuOptionType);
        
        return $skuOptionType;
    }
    
}

