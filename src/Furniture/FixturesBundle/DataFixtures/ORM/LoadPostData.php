<?php

namespace Furniture\FixturesBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Furniture\PostBundle\Entity\Post;
use Furniture\PostBundle\Entity\PostTranslation;
use Sylius\Bundle\FixturesBundle\DataFixtures\DataFixture;

class LoadPostData extends DataFixture
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        /** @var \Furniture\UserBundle\Entity\User $syliusUser */
        $syliusUser = $this->getReference('Sylius.User-Administrator');

        for ($i = 1; $i <= 100; $i++) {
            $post = new Post();

            $factory = rand(0, 1) ? $this->getRandomFactory() : null;

            $slug = str_replace(' ', '-', $this->faker->words(rand(5, 10), true));

            $post
                ->setDisplayName($this->faker->words(rand(3, 8), true))
                ->setSlug($slug)
                ->setCreator($syliusUser)
                ->setFactory($factory)
                ->setType(Post::TYPE_NEWS);

            $translation = new PostTranslation();
            $translation
                ->setTranslatable($post)
                ->setLocale($this->defaultLocale)
                ->setTitle($this->faker->words(rand(3, 10), true))
                ->setShortDescription($this->faker->text(400))
                ->setContent($this->faker->text(2000));

            $post->getTranslations()->add($translation);

            $manager->persist($post);
        }

        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 25;
    }

    /**
     * Get random factory
     *
     * @return \Furniture\FactoryBundle\Entity\Factory
     */
    private function getRandomFactory()
    {
        $factories = [
            'Selva',
            'Antonello Italia',
            'Kartell',
            'Arceos',
            'Passini',
            'i4 Mariani'
        ];

        $factory = $factories[rand(0, count($factories) - 1)];

        return $this->getReference('Furniture.factory.' . $factory);
    }
}
