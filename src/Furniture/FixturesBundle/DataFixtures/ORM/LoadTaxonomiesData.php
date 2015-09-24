<?php

namespace Furniture\FixturesBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\FixturesBundle\DataFixtures\DataFixture;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Model\TaxonomyInterface;
use Sylius\Bundle\FixturesBundle\DataFixtures\ORM\LoadTaxonomiesData as BaseLoadTaxonomiesData;

class LoadTaxonomiesData extends BaseLoadTaxonomiesData
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $manager->persist($this->createTaxonomy(
            array($this->defaultLocale => 'Category', 'es_ES' => 'Categoria'),
            array(
                array($this->defaultLocale => 'Table', 'es_ES' => 'Table'),
                array($this->defaultLocale => 'Chair', 'es_ES' => 'Chair'),
                array($this->defaultLocale => 'ArmChair', 'es_ES' => 'ArmChair'),
            )));

        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 10;
    }

}
