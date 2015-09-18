<?php

namespace Furniture\CompositionBundle;

use Furniture\CompositionBundle\DependencyInjection\CompositionExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class CompositionBundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function getContainerExtension()
    {
        if (!$this->extension) {
            $this->extension = new CompositionExtension();
        }

        return $this->extension;
    }
}
