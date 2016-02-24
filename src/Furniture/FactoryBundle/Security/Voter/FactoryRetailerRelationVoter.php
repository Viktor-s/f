<?php

namespace Furniture\FactoryBundle\Security\Voter;

use Furniture\UserBundle\Entity\User;
use Furniture\FactoryBundle\Entity\FactoryRetailerRelation;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class FactoryRetailerRelationVoter implements VoterInterface
{
    /**
     * {@inheritDoc}
     */
    public function supportsAttribute($attribute)
    {
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

        if (in_array('FACTORY_RETAILER_RELATION_LIST', $attributes) || in_array('FACTORY_RETAILER_RELATION_CREATE', $attributes)) {
            if ($user->isFactory() && $user->isFactoryAdmin()) {
                return self::ACCESS_GRANTED;
            }

            return self::ACCESS_DENIED;
        }
        
        if (in_array('FACTORY_RETAILER_RELATION_EDIT', $attributes) || in_array('FACTORY_RETAILER_RELATION_REMOVE', $attributes)) {
            
            if (!$object instanceof FactoryRetailerRelation) {
                return self::ACCESS_ABSTAIN;
            }
            
            if( !$object->isFactoryAccept() 
                    && $user->isRetailer()
                    && $user->getRetailerUserProfile()->isRetailerAdmin()
                    ){
                return self::ACCESS_GRANTED;
            }
            
            if(!$object->isRetailerAccept()
                    && $user->isFactory() && $user->isFactoryAdmin()
                    ){
                return self::ACCESS_GRANTED;
            }
            
            if ($object->isDeal() && $user->isFactory() && $user->isFactoryAdmin()) {
                return self::ACCESS_GRANTED;
            }
            
            return self::ACCESS_DENIED;
        }

        return self::ACCESS_ABSTAIN;
    }
}
