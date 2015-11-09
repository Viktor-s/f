<?php

namespace Furniture\FactoryBundle\Security\Voter;

use Furniture\CommonBundle\Entity\User;
use Furniture\ProductBundle\Entity\Product;
use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;

class ViewPriceVoter extends AbstractVoter
{
    /**
     * {@inheritDoc}
     */
    protected function getSupportedClasses()
    {
        return [Product::class];
    }

    /**
     * {@inheritDoc}
     */
    protected function getSupportedAttributes()
    {
        return ['VIEW_PRICE'];
    }

    /**
     * {@inheritDoc}
     */
    protected function isGranted($attribute, $object, $user = null)
    {
        /** @var \Furniture\ProductBundle\Entity\Product $object */
        if (!$user || !$user instanceof User) {
            return false;
        }

        $factory = $object->getFactory();

        if (!$user->isRetailer()) {
            // This is a no retailer. Check by defaults for factory
            return $factory->getDefaultRelation()
                ->isAccessProductsPrices();
        }

        // Search relation between factory and user
        $retailerRelation = $object->getFactory()->getRetailerRelationByRetailer($user->getRetailerProfile());

        $accessInDefaults = $factory->getDefaultRelation()->isAccessProductsPrices();

        if ($accessInDefaults) {
            return true;
        }

        if ($retailerRelation) {
            return $retailerRelation->isAccessProductsPrices();
        }

        return $accessInDefaults;
    }
}
