<?php

namespace Furniture\SpecificationBundle\Security\Voter;

use Furniture\UserBundle\Entity\User;
use Furniture\SpecificationBundle\Entity\Specification;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class SpecificationVoter implements VoterInterface
{
    /**
     * {@inheritDoc}
     */
    public function supportsAttribute($attribute)
    {
        return in_array($attribute, [
            'SPECIFICATION_LIST',
            'SPECIFICATION_CREATE',
            'VIEW',
            'EDIT',
            'REMOVE',
            'FINISH',
            'EXPORT'
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
            return self::ACCESS_ABSTAIN;
        }

        if (in_array('SPECIFICATION_LIST', $attributes) || in_array('SPECIFICATION_CREATE', $attributes)) {
            return self::ACCESS_GRANTED;
        }

        if(!$object instanceof Specification) {
            return self::ACCESS_ABSTAIN;
        }
        
        if (in_array('EDIT', $attributes) || in_array('REMOVE', $attributes) || in_array('FINISH', $attributes) || in_array('EXPORT', $attributes) || in_array('VIEW', $attributes)) {
            $owner = $object->getCreator();
            if (
                    $user->getRetailerUserProfile()->isRetailerAdmin()
                    && $user->getRetailerUserProfile()->getRetailerProfile()->getId() == $owner->getRetailerProfile()->getId()
                    ) {
                return self::ACCESS_GRANTED;
            }

            if (!$object || !$object instanceof Specification) {
                return self::ACCESS_ABSTAIN;
            }

            if($owner->getId() == $user->getRetailerUserProfile()->getId()) {
                return self::ACCESS_GRANTED;
            }

            return self::ACCESS_DENIED;
        }

        return self::ACCESS_ABSTAIN;
    }
}
