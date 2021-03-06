<?php

namespace Furniture\FixturesBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Furniture\RetailerBundle\Entity\RetailerProfile;
use Sylius\Bundle\FixturesBundle\DataFixtures\DataFixture;

class LoadRetailerProfileData extends DataFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        foreach (self::getRetailerProfiles() as $key => $profileInfo) {
            $profile = new RetailerProfile();

            $profile
                ->setName($profileInfo['name'])
                ->setPhones($profileInfo['phones'])
                ->setEmails($profileInfo['emails'])
                ->setCurrentLocale($this->defaultLocale)
                ->setFallbackLocale($this->defaultLocale)
                ->translate()->setAddress($profileInfo['address']);

            $manager->persist($profile);
            $this->setReference('retailer_profile:' . $key, $profile);
        }

        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 0;
    }

    /**
     * Get retailer profiles
     *
     * @return array
     */
    public static function getRetailerProfiles()
    {
        return [
            'stol_i_stul' => [
                'name' => 'Stol i Stul',
                'address' => 'Ukraine, Kyiv, Naberezhna str., apartment #2',
                'phones' => ['+380971086503', '+380986519343'],
                'emails' => ['mail@stolistul.com.ua'],
            ],

            'dubok' => [
                'name' => 'dubok',
                'address' => 'Ukraine, Lviv, Tarasa Shevchenka str., apartment #331',
                'phones' => ['+380937854565'],
                'emails' => ['dubok.mebel@ukr.net']
            ]
        ];
    }
}
