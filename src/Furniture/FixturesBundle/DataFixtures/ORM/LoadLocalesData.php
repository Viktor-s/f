<?php

namespace Furniture\FixturesBundle\DataFixtures\ORM;

use Sylius\Bundle\FixturesBundle\DataFixtures\ORM\LoadLocalesData as BaseLoadLocalesData;

class LoadLocalesData extends BaseLoadLocalesData
{
    private $locales = array(
        'en_US' => true,
        'en_GB' => true,
        'es_ES' => true,
        'de_DE' => true,
        'it_IT' => false,
        'pl_PL' => true,
    );
}
