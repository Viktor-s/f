<?php

namespace Furniture\FixturesBundle\DataFixtures\ORM;

use Sylius\Bundle\FixturesBundle\DataFixtures\DataFixture;
use \Doctrine\Common\Persistence\ObjectManager;

class LoadCompositeCollectionData extends DataFixture 
{
    
    public function getOrder() {
        return 19;
    }
    
    public function load(ObjectManager $manager) {
        
        $compositeCollection = $this->createCollection('Mitchell');
        $manager->persist($compositeCollection);
        
        $manager->flush();
        
        $compositeCollection = $this->createCollection('Gallery');
        $manager->persist($compositeCollection);
        
        $compositeCollection = $this->createCollection('Takat');
        $manager->persist($compositeCollection);
        
        $compositeCollection = $this->createCollection('Holtom');
        $manager->persist($compositeCollection);
        
        $compositeCollection = $this->createCollection('Natura');
        $manager->persist($compositeCollection);
        
        $compositeCollection = $this->createCollection('Ashworth Customizable Desk System');
        $manager->persist($compositeCollection);
        
        $manager->flush();
        
    }
    
    /**
     * 
     * @param string $name
     * @return \Sylius\Component\Translation\Model\AbstractTranslatable\CompositeCollection
     */
    protected function createCollection($name){
        /* @var $compositeCollection \Sylius\Component\Translation\Model\AbstractTranslatable\CompositeCollection */
        $compositeCollection = $this->get('furniture.repository.composite_collection')->createNew();
        $compositeCollection->setName($name);
        
        $this->setReference('Furniture.composite_collection.'.$name, $compositeCollection);
        
        return $compositeCollection;
    }
    
}

