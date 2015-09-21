<?php

namespace Furniture\FrontendBundle;

use Furniture\FrontendBundle\DependencyInjection\FrontendExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class FrontendBundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function getContainerExtension()
    {
        if (!$this->extension) {
            $this->extension = new FrontendExtension();
        }

        return $this->extension;
    }
}
