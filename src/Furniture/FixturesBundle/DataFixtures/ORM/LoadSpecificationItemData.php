<?php

namespace Furniture\FixturesBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Furniture\SpecificationBundle\Entity\SpecificationItem;
use Furniture\SpecificationBundle\Entity\SkuSpecificationItem;

class LoadSpecificationItemData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $specifications = $this->getSpecifications();

        $faker = Factory::create();

        foreach ($specifications as $specificationIndex => $specification) {
            $countVariants = rand(2, 10);

            for ($i = 1; $i <= $countVariants; $i++) {
                $variantReferenceId = rand(1, SYLIUS_FIXTURES_TOTAL_VARIANTS);
                /** @var \Furniture\ProductBundle\Entity\ProductVariant $variant */
                $variant = $this->getReference('Sylius.Variant-' . $variantReferenceId);

                if (rand(0, 3)) {
                    $quantity = 1;
                } else {
                    $quantity = rand(2, 10);
                }

                if (rand(0, 1)) {
                    $note = $faker->text(50);
                } else {
                    $note = null;
                }

                $skuSpecificationItem = new SkuSpecificationItem();
                $skuSpecificationItem->setProductVariant($variant)
                        ;
                
                
                $specificationItem = new SpecificationItem();
                $specificationItem
                    ->setSkuItem($skuSpecificationItem)
                    ->setSpecification($specification)
                    ->setQuantity($quantity)
                    ->setNote($note)
                        ;

                $manager->persist($specificationItem);
            }

            $manager->flush();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 60;
    }

    /**
     * Get specifications
     *
     * @return array|\Furniture\SpecificationBundle\Entity\Specification[]
     */
    private function getSpecifications()
    {
        $specifications = [];

        for ($i = 1; $i <= 5; $i++) {
            $specifications[$i] = $this->getReference('specification:' . $i);
        }

        return $specifications;
    }
}
