<?php

namespace Furniture\FixturesBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Furniture\FactoryBundle\Entity\Factory;
use Furniture\FactoryBundle\Entity\FactoryRetailerRelation;
use Furniture\RetailerBundle\Entity\RetailerProfile;

class LoadRetailerFactoryRelationData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @var ObjectManager
     */
    private $manager;

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        $this->createRelation('stol_i_stul', 'Selva', true, true, true, true, 10);
        $this->createRelation('stol_i_stul', 'Antonello Italia', true, false, true, true, 0);
        $this->createRelation('stol_i_stul', 'Kartell', true, false, true, false, 0);
        $this->createRelation('stol_i_stul', 'Arceos', true, true, false, true, 15);
        $this->createRelation('stol_i_stul', 'Passini', true, false, false, true, 10);

        $this->createRelation('dubok', 'Selva', false, false, true, true, 0);
    }

    /**
     * Create relation between retailer and factory
     *
     * @param string $retailer
     * @param string $factory
     * @param bool   $productView
     * @param bool   $priceView
     * @param bool   $retailerAccept
     * @param bool   $factoryAccept
     * @param int    $discount
     *
     * @return FactoryRetailerRelation
     */
    private function createRelation(
        $retailer,
        $factory,
        $productView,
        $priceView,
        $retailerAccept,
        $factoryAccept,
        $discount
    )
    {
        $retailer = $this->getReference('retailer_profile:' . $retailer);
        $factory = $this->getReference('Furniture.factory.' . $factory);

        $relation = $this->createRelationObject($retailer, $factory, $productView, $priceView, $retailerAccept, $factoryAccept, $discount);
        $this->manager->persist($relation);
        $this->manager->flush();
    }

    /**
     * Create object relation between retailer and factory
     *
     * @param RetailerProfile $retailer
     * @param Factory         $factory
     * @param bool            $productView
     * @param bool            $priceView
     * @param bool            $retailerAccept
     * @param bool            $factoryAccept
     * @param int             $discount
     *
     * @return FactoryRetailerRelation
     */
    private function createRelationObject(
        RetailerProfile $retailer,
        Factory $factory,
        $productView,
        $priceView,
        $retailerAccept,
        $factoryAccept,
        $discount
    )
    {
        $relation = new FactoryRetailerRelation();

        $relation
            ->setRetailer($retailer)
            ->setFactory($factory)
            ->setAccessProducts($productView)
            ->setAccessProductsPrices($priceView)
            ->setRetailerAccept($retailerAccept)
            ->setFactoryAccept($factoryAccept)
            ->setDiscount($discount);

        return $relation;
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 25;
    }
}
