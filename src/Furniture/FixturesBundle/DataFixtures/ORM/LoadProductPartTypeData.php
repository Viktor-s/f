<?php
namespace Furniture\FixturesBundle\DataFixtures\ORM;

use Sylius\Bundle\FixturesBundle\DataFixtures\DataFixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadProductPartTypeData extends DataFixture 
{
    
    /**
     * List of product part types
     * @var type 
     */
    private $productPartTypeCodes = [
        'seat',
        'back',
        'Upholstery',
        'Top',
        'Frame',
        'Legs'
    ];

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 49;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        foreach ($this->productPartTypeCodes as $code){
            $productPartType = $this->createProductPartType($code);
            $manager->persist($productPartType);
            $manager->flush();
        }
    }
    
    /**
     * 
     * @param string $code
     * @return \Furniture\ProductBundle\Entity\ProductPartType
     */
    protected function createProductPartType($code)
    {
        /* @var $productPartType \Furniture\ProductBundle\Entity\ProductPartType */
        $productPartType = $this->get('Furniture.repository.product_part_type')->createNew();
        $productPartType->setCode($code);
        $this->setReference('Furniture.product_part_type.'.$code, $productPartType);
        return $productPartType;
    }
    
}

