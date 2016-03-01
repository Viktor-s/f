<?php

namespace Furniture\FactoryBundle\Security\Voter;

use Furniture\UserBundle\Entity\User;
use Furniture\FactoryBundle\Entity\Factory;
use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;

class ViewProductsVoter extends AbstractVoter
{
    /**
     * {@inheritDoc}
     */
    protected function getSupportedClasses()
    {
        return [Factory::class];
    }

    /**
     * {@inheritDoc}
     */
    protected function getSupportedAttributes()
    {
        return ['VIEW_PRODUCTS'];
    }

    /**
     * {@inheritDoc}
     */
    protected function isGranted($attribute, $object, $user = null)
    {
        /** @var \Furniture\FactoryBundle\Entity\Factory $object */
        if (!$user || !$user instanceof User) {
            return false;
        }

        if ($user->isNoRetailer()) {
            // This is a no retailer. Check by defaults for factory
            return $object->getDefaultRelation()
                ->isAccessProducts();
        }

        $retailerProfile = $user->getRetailerUserProfile()->getRetailerProfile();

        // Search relation between factory and user
        $retailerRelation = $object->getRetailerRelationByRetailer($retailerProfile);

        $accessInDefaults = $object->getDefaultRelation()->isAccessProducts();

        if ($accessInDefaults) {
            return true;
        }

        if ($retailerRelation) {
            return $retailerRelation->isDeal() && $retailerRelation->isAccessProducts();
        }

        return $accessInDefaults;
    }
}
