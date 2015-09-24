<?php

namespace Furniture\FixturesBundle\DataFixtures\ORM;

use Sylius\Bundle\FixturesBundle\DataFixtures\DataFixture;
use \Doctrine\Common\Persistence\ObjectManager;

class LoadFactoriesData extends DataFixture
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        
        $factory = $this->createFactory(
                'Selva',
            [
                $this->defaultLocale => [
                    'description' => 'Selva factory description'
                ]
            ]);
        $manager->persist($factory);
        
        $factory = $this->createFactory(
                'Antonello Italia',
            [
                $this->defaultLocale => [
                    'description' => 'Antonello Italia factory description'
                ]
            ]);
        $manager->persist($factory);
        
        $factory = $this->createFactory(
                'Kartell',
            [
                $this->defaultLocale => [
                    'description' => 'Kartell factory description'
                ]
            ]);
        
        $manager->persist($factory);
        
        $factory = $this->createFactory(
                'Arceos',
            [
                $this->defaultLocale => [
                    'description' => 'Arceos factory description'
                ]
            ]);
        $manager->persist($factory);
        
        $factory = $this->createFactory(
                'Passini',
            [
                $this->defaultLocale => [
                    'description' => 'Passini factory description'
                ]
            ]);
        $manager->persist($factory);
        
        $factory = $this->createFactory(
                'i4 Mariani',
            [
                $this->defaultLocale => [
                    'description' => 'i4 Mariani factory description'
                ]
            ]);
        $manager->persist($factory);
        
        $manager->flush();
    }

    /**
     * Create a new factory
     *
     * @param string $name
     * @param array  $translations
     *
     * @return \Furniture\FactoryBundle\Entity\Factory
     */
    private function createFactory($name, array $translations)
    {
        /* @var $factory \Furniture\FactoryBundle\Entity\Factory */
        $factory = $this->get('Furniture.repository.factory')->createNew();
        $factory->setName($name);
        
        foreach ($translations as $locale => $presentation) {
            $factory->setCurrentLocale($locale);
            $factory->setDescription($presentation['description']);
        }
        
        $this->setReference('Furniture.factory.'.$name, $factory);
        
        return $factory;
        
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 19;
    }
}
