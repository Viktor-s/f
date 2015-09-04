<?php

namespace Furniture\SearchBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class SearchBundle extends Bundle
{
    
    public function getParent() {
        return 'SyliusSearchBundle';
    }
    
}
