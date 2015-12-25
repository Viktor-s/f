<?php

namespace Furniture\FixturesBundle\DataFixtures\ORM;

use Sylius\Bundle\FixturesBundle\DataFixtures\DataFixture;
use \Doctrine\Common\Persistence\ObjectManager;

class LoadProductPartMaterialOption extends DataFixture {
    
    /**
     * List of product part material option names
     * @var array
     */
    private $productPartMaterialOptions = [
        'category',
        'code',
        'Color',
    ];


    public function getOrder() {
        return 19;
    }
    
    public function load(ObjectManager $manager) {
        
        foreach($this->productPartMaterialOptions as $productPartMaterialOptionName){
            $roductPartMaterialOption = $this->createEntity($productPartMaterialOptionName);
            $manager->persist($roductPartMaterialOption);
        }
        
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

