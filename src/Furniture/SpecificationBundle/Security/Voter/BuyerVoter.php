<?php

namespace Furniture\SpecificationBundle\Security\Voter;

use Furniture\CommonBundle\Entity\User;
use Furniture\SpecificationBundle\Entity\Buyer;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class BuyerVoter implements VoterInterface
{
    /**
     * {@inheritDoc}
     */
    public function supportsAttribute($attribute)
    {
        return in_array($attribute, [
            'SPECIFICATION_BUYER_CREATE',
            'SPECIFICATION_BUYER_LIST',
            'EDIT',
            'REMOVE'
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function supportsClass($class)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function vote(TokenInterface $token, $object, array $attributes)
    {
        
        $user = $token->getUser();

        if (!$user || !$user instanceof User) {
            return self::ACCESS_ABSTAIN;
        }

        if (!$user->isRetailer()) {
            // Access only for retailers
            return self::ACCESS_ABSTAIN;
        }

        if (in_array('SPECIFICATION_BUYER_LIST', $attributes) || in_array('SPECIFICATION_BUYER_LIST', $attributes)) {
            return self::ACCESS_GRANTED;
        }

        if (!$object instanceof Buyer) {
            return self::ACCESS_ABSTAIN;
        }

        if (in_array('EDIT', $attributes) || in_array('REMOVE', $attributes)) {
            $creator = $object->getCreator();
            if ($user->getRetailerUserProfile()->isRetailerAdmin()
                    && $user->getRetailerUserProfile()->getRetailerProfile()->getId() == $creator->getRetailerProfile()->getId()
                    ) {
                return self::ACCESS_GRANTED;
            }

            if ($creator->getId() == $user->getRetailerUserProfile()->getId()) {
                return self::ACCESS_GRANTED;
            } else {
                return self::ACCESS_DENIED;
            }
        }

        return self::ACCESS_ABSTAIN;
    }
}
