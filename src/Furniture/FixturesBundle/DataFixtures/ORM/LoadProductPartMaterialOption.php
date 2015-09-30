<?php

namespace Furniture\FixturesBundle\DataFixtures\ORM;

use Sylius\Bundle\FixturesBundle\DataFixtures\DataFixture;
use \Doctrine\Common\Persistence\ObjectManager;

class LoadProductPartMaterialOption extends DataFixture {
    
    public function getOrder() {
        return 19;
    }
    
    public function load(ObjectManager $manager) {
        
        $roductPartMaterialOption = $this->createEntity('Material category');
        $manager->persist($roductPartMaterialOption);
        
        $roductPartMaterialOption = $this->createEntity('Material name');
        $manager->persist($roductPartMaterialOption);
        
        $manager->flush();
    }
    
    /**
     * 
     * @param string $name
     * @return \Furniture\ProductBundle\Entity\ProductPartMaterialOption
     */
    public function createEntity($name){
        /* @var $roductPartMaterialOption Furniture\ProductBundle\Entity\ProductPartMaterialOption */
        $roductPartMaterialOption = $this->get('furniture.repository.product_part_material_option')->createNew();
        $roductPartMaterialOption->setName($name);
        $this->setReference('Furniture.product_part_material_option.'.$name, $roductPartMaterialOption);
        return $roductPartMaterialOption;
    }
    
}

