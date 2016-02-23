<?php

namespace Furniture\FactoryBundle\Twig;

use Furniture\FactoryBundle\Entity\Factory;
use Furniture\FactoryBundle\FactoryRemoval\FactoryRemovalChecker;

class FactoryExtension extends \Twig_Extension
{
    /**
     * @var FactoryRemovalChecker
     */
    private $factoryRemovalChecker;

    /**
     * Construct
     *
     * @param FactoryRemovalChecker $factoryRemovalChecker
     */
    public function __construct(FactoryRemovalChecker $factoryRemovalChecker)
    {
        $this->factoryRemovalChecker = $factoryRemovalChecker;
    }

    /**
     * {@inheritDoc}
     */
    public function getFunctions()
    {
        return [
            'is_factory_can_hard_remove' => new \Twig_Function_Method($this, 'isFactoryCanHardRemove')
        ];
    }

    /**
     * Is factory can hard remove?
     *
     * @param Factory $factory
     *
     * @return bool
     */
    public function isFactoryCanHardRemove(Factory $factory)
    {
        return $this->factoryRemovalChecker->canHardRemove($factory)->canRemove();
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'factory';
    }
}
