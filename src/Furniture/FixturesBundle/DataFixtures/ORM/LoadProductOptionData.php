<?php

namespace Furniture\FixturesBundle\DataFixtures\ORM;

use Sylius\Bundle\FixturesBundle\DataFixtures\ORM\LoadProductOptionData as BaseLoadProductOptionData;
use Doctrine\Common\Persistence\ObjectManager;

class LoadProductOptionData extends BaseLoadProductOptionData {
    
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        //table tops
        $option = $this->createOption(
            'table_tops',
            array($this->defaultLocale => 'Tops', 'es_ES' => 'Tops'),
            array('Ceramic tops', 'Smoked oak', 'Canaletto walnut', 'Grey oak', 'Wengè')
        );
        $manager->persist($option);

        //table legs
        $option = $this->createOption(
            'chair_legs',
            array($this->defaultLocale => 'Legs', 'es_ES' => 'Legs'),
            array(
                'cabriole', 
                'Flemish scroll',
                'saber',
                'spiral',
                'spider',
                'reeded'
                )
        );
        $manager->persist($option);
        
        //table legs
        $option = $this->createOption(
            'table_legs',
            array($this->defaultLocale => 'Legs', 'es_ES' => 'Legs'),
            array(
                'Shining inox steel legsa', 
                'Matt lacquered wooden legs', 
                'Shining lacquered wooden legs',
                'Smoked oak',
                'Canaletto walnut',
                'Grey oak'
                )
        );
        $manager->persist($option);

        // frame option.
        $option = $this->createOption(
            'frame',
            array($this->defaultLocale => 'Size', 'es_ES' => 'Talla'),
            array(
                'ASH GREY VARNISHED',
                'DOVE GREY VARNISHED',
                'BLACK VARNISHED',
                'WHITE VARNISHED'
                ));
        $manager->persist($option);

        $manager->flush();
    }
    
}

