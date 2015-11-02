<?php

namespace Furniture\CommonBundle\Twig;

class CommonExtension extends \Twig_Extension
{
    /**
     * {@inheritDoc}
     */
    public function getFilters()
    {
        return [
            'md5' => new \Twig_Filter_Function('md5')
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'furniture_common';
    }
}
