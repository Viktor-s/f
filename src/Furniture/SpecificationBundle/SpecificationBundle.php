<?php

namespace Furniture\SpecificationBundle;

use Furniture\SpecificationBundle\DependencyInjection\SpecificationExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SpecificationBundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function getContainerExtension()
    {
        if (!$this->extension) {
            $this->extension = new SpecificationExtension();
        }

        return $this->extension;
    }
}
