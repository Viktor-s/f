<?php

namespace Furniture\FixturesBundle\DataFixtures\ORM;

use Sylius\Bundle\FixturesBundle\DataFixtures\DataFixture;
use \Doctrine\Common\Persistence\ObjectManager;

class LoadSkuOptionTypesData extends DataFixture
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $skuOptionType = $this->createSkuOptionType([
            $this->defaultLocale => [
                'name' => 'Size'
                ]
        ]);

        $manager->persist($skuOptionType);
        $manager->flush();
    }

    /**
     * Create a new sky option type
     *
     * @param array $translations
     *
     * @return \Furniture\SkuOptionBundle\Entity\SkuOptionType
     */
    public function createSkuOptionType(array $translations)
    {
        /* @var \Furniture\SkuOptionBundle\Entity\SkuOptionType $skuOptionType */
        $skuOptionType = $this->get('Furniture.repository.sku_option')->createNew();
        
        foreach ($translations as $locale => $presentation) {
            $skuOptionType->setCurrentLocale($locale);
            $skuOptionType->setName($presentation['name']);
        }
        
        $this->setReference('Furniture.sku_option.'.$skuOptionType->getName(), $skuOptionType);
        
        return $skuOptionType;
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 19;
    }
}
