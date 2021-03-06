<?php

namespace Furniture\FixturesBundle\DataFixtures\ORM;

use Furniture\CompositionBundle\Entity\CompositeCollection;
use Furniture\CompositionBundle\Entity\CompositeCollectionTranslation;
use Sylius\Bundle\FixturesBundle\DataFixtures\DataFixture;
use \Doctrine\Common\Persistence\ObjectManager;

class LoadCompositeCollectionData extends DataFixture 
{

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 19;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
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
     * Create composite collection
     *
     * @param string $name
     *
     * @return CompositeCollection
     */
    private function createCollection($name)
    {
        $compositeCollection = $this->get('furniture.repository.composite_collection')->createNew();;
        $compositeCollection->setName($name);
        $compositeCollection->setCurrentLocale($this->defaultLocale);
        $compositeCollection->setPresentation('presentation: '.$name);
        $compositeCollection->setDescription('description: '.$name);
        $this->setReference('Furniture.composite_collection.'.$name, $compositeCollection);
        
        return $compositeCollection;
    }
}
