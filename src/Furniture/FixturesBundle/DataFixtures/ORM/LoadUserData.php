<?php

namespace Furniture\FixturesBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Furniture\FactoryBundle\Entity\FactoryUserRelation;
use Sylius\Bundle\FixturesBundle\DataFixtures\ORM\LoadUsersData as BaseLoadUserData;

class LoadUserData extends BaseLoadUserData
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->createAdministrator($manager);
        $this->createCustomer($manager);

        $this->createContentUsers($manager);
        $this->createFactoryAdminUsers($manager);
        $this->createRelationsBetweenUserAndFactory($manager);

        $manager->flush();
    }

    /**
     * Create content users
     *
     * @param ObjectManager $manager
     */
    private function createContentUsers(ObjectManager $manager)
    {
        for ($i = 1; $i <= 5; $i++) {
            $email = 'user' . $i . '@content-user.com';

            /** @var \Furniture\CommonBundle\Entity\User $user */
            $user = $this->createUser(
                $email,
                'user' . $i,
                true
            );

            $user->addAuthorizationRole($this->get('sylius.repository.role')->findOneBy(array('code' => 'content_user')));
            $this->setReference('user:content:' . $i, $user);

            $manager->persist($user);
        }

        $manager->flush();
    }

    /**
     * Create content users
     *
     * @param ObjectManager $manager
     */
    private function createFactoryAdminUsers(ObjectManager $manager)
    {
        for ($i = 1; $i <= 5; $i++) {
            $email = 'user' . $i . '@factory-admin.com';

            /** @var \Furniture\CommonBundle\Entity\User $user */
            $user = $this->createUser(
                $email,
                'user' . $i,
                true
            );

            if ($i == 1) {
                // Add the factory for user
                $user->setFactory($this->getReference('Furniture.factory.Selva'));
            }

            $user->addAuthorizationRole($this->get('sylius.repository.role')->findOneBy(array('code' => 'factory_admin')));

            $this->setReference('user:factory:' . $i, $user);

            $manager->persist($user);
        }

        $manager->flush();
    }

    /**
     * Create a relations between user and factory
     *
     * @param ObjectManager $manager
     */
    private function createRelationsBetweenUserAndFactory(ObjectManager $manager)
    {
        /** @var \Furniture\FactoryBundle\Entity\Factory $selvaFactory */
        $selvaFactory = $this->getReference('Furniture.factory.Selva');
        /** @var \Furniture\FactoryBundle\Entity\Factory $antonelloItaliaFactory */
        $antonelloItaliaFactory = $this->getReference('Furniture.factory.Antonello Italia');
        /** @var \Furniture\FactoryBundle\Entity\Factory $kartellFactory */
        $kartellFactory = $this->getReference('Furniture.factory.Kartell');

        // Write relation for user:content:1 (Request to factory)
        $factoryUserRelation = new FactoryUserRelation();
        $factoryUserRelation
            ->setUser($this->getReference('user:content:1'))
            ->setFactory($selvaFactory)
            ->setActive(true)
            ->setFactoryAccept(false)
            ->setUserAccept(true);

        $manager->persist($factoryUserRelation);

        // Write relation for user:content:1 (Request from factory Antonello Italia)
        $factoryUserRelation = new FactoryUserRelation();
        $factoryUserRelation
            ->setUser($this->getReference('user:content:1'))
            ->setFactory($antonelloItaliaFactory)
            ->setActive(true)
            ->setFactoryAccept(true)
            ->setUserAccept(false)
            ->setAccessProducts(true)
            ->setAccessProductsPrices(true)
            ->setDiscount(5);

        $manager->persist($factoryUserRelation);

        // Write relation for user:content:1 (Authorized factory with all rights)
        $factoryUserRelation = new FactoryUserRelation();
        $factoryUserRelation
            ->setUser($this->getReference('user:content:1'))
            ->setFactory($kartellFactory)
            ->setAccessProductsPrices(true)
            ->setFactoryAccept(true)
            ->setUserAccept(true)
            ->setAccessProducts(true)
            ->setAccessProductsPrices(true)
            ->setDiscount(15);

        $manager->persist($factoryUserRelation);

        // Write relation for user:content:2 (Factory request to user with all rights)
        $factoryUserRelation = new FactoryUserRelation();
        $factoryUserRelation
            ->setUser($this->getReference('user:content:2'))
            ->setFactory($selvaFactory)
            ->setActive(true)
            ->setAccessProducts(true)
            ->setAccessProductsPrices(true)
            ->setFactoryAccept(true)
            ->setUserAccept(false);

        $manager->persist($factoryUserRelation);

        // Write relation for user:content:3 (Factory request to user without product price rights)
        $factoryUserRelation = new FactoryUserRelation();
        $factoryUserRelation
            ->setUser($this->getReference('user:content:3'))
            ->setFactory($selvaFactory)
            ->setActive(true)
            ->setAccessProducts(true)
            ->setAccessProductsPrices(false)
            ->setFactoryAccept(true)
            ->setUserAccept(false);

        $manager->persist($factoryUserRelation);

        // Write relation for user:content:4 (Authorized user with all rights)
        $factoryUserRelation = new FactoryUserRelation();
        $factoryUserRelation
            ->setUser($this->getReference('user:content:4'))
            ->setFactory($selvaFactory)
            ->setActive(true)
            ->setAccessProducts(true)
            ->setAccessProductsPrices(true)
            ->setFactoryAccept(true)
            ->setUserAccept(true);

        $manager->persist($factoryUserRelation);

        // Write relation for user:content:5 (Authorized user without product price right)
        $factoryUserRelation = new FactoryUserRelation();
        $factoryUserRelation
            ->setUser($this->getReference('user:content:5'))
            ->setFactory($selvaFactory)
            ->setActive(true)
            ->setAccessProducts(true)
            ->setAccessProductsPrices(false)
            ->setFactoryAccept(true)
            ->setUserAccept(true);

        $manager->persist($factoryUserRelation);
        $manager->flush();

    }

    /**
     * Create customer
     */
    private function createCustomer(ObjectManager $manager)
    {
        $customer = $this->getCustomerRepository()->createNew();
        $customer->setFirstname($this->faker->firstName);
        $customer->setLastname($this->faker->lastName);
        $customer->setEmail('customer@email.com');
        $manager->persist($customer);
        $manager->flush();
    }

    /**
     * Create a administrator
     */
    private function createAdministrator(ObjectManager $manager)
    {
        $user = $this->createUser(
            'sylius@example.com',
            'sylius',
            true,
            array('ROLE_USER', 'ROLE_SYLIUS_ADMIN', 'ROLE_ADMINISTRATION_ACCESS')
        );
        $user->addAuthorizationRole($this->get('sylius.repository.role')->findOneBy(array('code' => 'administrator')));

        $manager->persist($user);
        $manager->flush();

        $this->setReference('Sylius.User-Administrator', $user);
    }
}
