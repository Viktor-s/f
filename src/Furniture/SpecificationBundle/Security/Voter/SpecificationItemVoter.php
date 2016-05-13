<?php

namespace Furniture\SpecificationBundle\Security\Voter;

use Furniture\SpecificationBundle\Entity\SpecificationItem;
use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;

class SpecificationItemVoter extends AbstractVoter
{
    /**
     * {@inheritDoc}
     */
    protected function getSupportedClasses()
    {
        return [SpecificationItem::class];
    }

    /**
     * {@inheritDoc}
     */
    protected function getSupportedAttributes()
    {
        return ['VIEW', 'CREATE', 'EDIT', 'REMOVE'];
    }

    /**
     * {@inheritDoc}
     */
    protected function isGranted($attribute, $object, $user = null)
    {
        /** @var \Furniture\UserBundle\Entity\User $user */
        if ($user->isNoRetailer()) {
            // Only retailer have grants to custom specification item.
            return false;
        }

        if ($attribute == 'CREATE') {
            return true;
        }

        if (!$object instanceof SpecificationItem) {
            return false;
        }

        /** @var SpecificationItem $object */
        // First check: demo account
        $retailerProfile = $user->getRetailerUserProfile()->getRetailerProfile();
        if ($retailerProfile->isDemo()) {
            $skuItem= $object->getSkuItem();

            if ($skuItem) {
                $factory = $skuItem->getProductVariant()->getProduct()->getFactory();

                if ($factory) {
                    if ($retailerProfile->hasDemoFactory($factory)) {
                        // The active retailer profile not have rights to this factory via demo account
                        return false;
                    }
                }
            }
        }

        // Second check: owner or creator
        $owner = $object->getSpecification()->getCreator();

        $retailerAdmin = $user->getRetailerUserProfile()->isRetailerAdmin();
        $isOwner = $retailerProfile->getId() == $owner->getRetailerProfile()->getId();

        if ($retailerAdmin && $isOwner) {
            return true;
        }

        if (!$user) {
            return false;
        }

        return $user->getRetailerUserProfile()->getId() == $owner->getId();
    }
}
