<?php

namespace Furniture\FixturesBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Sylius\Bundle\FixturesBundle\DataFixtures\ORM\LoadUsersData as BaseLoadUserData;

class LoadUserData extends BaseLoadUserData
{
    private $usernames = [];
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->createAdministrator($manager);
        $this->createCustomer($manager);

        $this->createContentUsers($manager);
        $this->createFactoryAdminUsers($manager);
    }

    /**
     * Create content users
     *
     * @param ObjectManager $manager
     */
    private function createContentUsers(ObjectManager $manager)
    {
        for ($i = 1; $i <= 5; $i++) {
            $username = $this->randomUsername();
            $email = 'user' . $i . '@content-user.com';

            /** @var \Furniture\CommonBundle\Entity\User $user */
            $user = $this->createUser(
                $email,
                'user' . $i,
                true
            );

            $user->addAuthorizationRole($this->get('sylius.repository.role')->findOneBy(array('code' => 'content_user')));

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
            $username = $this->randomUsername();
            $email = 'user' . $i . '@factory-admin.com';

            /** @var \Furniture\CommonBundle\Entity\User $user */
            $user = $this->createUser(
                $email,
                'user' . $i,
                true
            );

            $user->addAuthorizationRole($this->get('sylius.repository.role')->findOneBy(array('code' => 'factory_admin')));

            $manager->persist($user);
        }

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

    /**
     * Get random username
     *
     * @return string
     */
    private function randomUsername()
    {
        $username = $this->faker->username;

        while (isset($this->usernames[$username])) {
            $username = $this->faker->username;
        }

        return $username;
    }
}
