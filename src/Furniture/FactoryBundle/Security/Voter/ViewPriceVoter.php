<?php

namespace Furniture\FactoryBundle\Security\Voter;

use Furniture\CommonBundle\Entity\User;
use Furniture\ProductBundle\Entity\Product;
use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;
use Symfony\Component\Security\Core\User\UserInterface;

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

        if (!$user->isContentUser()) {
            // This is a no content user. Check by defaults for factory
            return $factory->getDefaultRelation()
                ->isAccessProductsPrices();
        }

        // Search relation between factory and user
        $userRelation = $object->getFactory()->getUserRelationByUser($user);

        $accessInDefaults = $factory->getDefaultRelation()->isAccessProductsPrices();

        if ($accessInDefaults) {
            return true;
        }

        if ($userRelation) {
            return $userRelation->isAccessProductsPrices();
        }

        return $accessInDefaults;
    }
}
