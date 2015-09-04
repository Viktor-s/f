<?php

namespace Furniture\VariationBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class VariationBundle extends Bundle
{
    public function getParent() {
        return 'SyliusVariationBundle';
    }
}
