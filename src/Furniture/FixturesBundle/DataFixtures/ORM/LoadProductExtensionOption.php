<?php

namespace Furniture\FixturesBundle\DataFixtures\ORM;

use Sylius\Bundle\FixturesBundle\DataFixtures\DataFixture;
use \Doctrine\Common\Persistence\ObjectManager;

class LoadProductExtensionOption extends DataFixture {
    
    public function getOrder() {
        return 19;
    }
    
    public function load(ObjectManager $manager) {
        
        $productExtensionOption = $this->createEntity('Material category');
        $manager->persist($productExtensionOption);
        
        $productExtensionOption = $this->createEntity('Material name');
        $manager->persist($productExtensionOption);
        
        $manager->flush();
    }
    
    /**
     * 
     * @param string $name
     * @return \Furniture\ProductBundle\Entity\ProductExtensionOption
     */
    public function createEntity($name){
        /* @var $productExtensionOption Furniture\ProductBundle\Entity\ProductExtensionOption */
        $productExtensionOption = $this->get('furniture.repository.product_extension_option')->createNew();
        $productExtensionOption->setName($name);
        $this->setReference('Furniture.product_extension_option.'.$name, $productExtensionOption);
        return $productExtensionOption;
    }
    
}

