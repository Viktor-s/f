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
        /** @var \Furniture\CommonBundle\Entity\User $user */
        if ($user->isNoRetailer()) {
            // Only retailer have grants to custom specification item.
            return false;
        }

        if ($attribute == 'CREATE') {
            return true;
        }

        if ($user->getRetailerUserProfile()->isRetailerAdmin()) {
            return true;
        }

        /** @var SpecificationItem $object */
        $owner = $object->getSpecification()->getCreator();

        if (!$user) {
            return false;
        }

        return $user->getId() == $owner->getId();
    }
}
