<?php

namespace Furniture\FixturesBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\FixturesBundle\DataFixtures\ORM\LoadProductAttributeData as BaseLoadProductAttributeData;

class LoadProductAttributeData extends BaseLoadProductAttributeData
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $attribute = $this->createAttribute('Designer', array($this->defaultLocale => 'Designer', 'es_ES' => 'Designer'));
        $manager->persist($attribute);

        $manager->flush();
    }

}
