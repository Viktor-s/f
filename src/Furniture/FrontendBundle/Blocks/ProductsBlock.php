<?php

namespace Furniture\FrontendBundle\Blocks;

use Furniture\FrontendBundle\Repository\ProductRepository;
use Sonata\BlockBundle\Block\BaseBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;

class ProductsBlock extends BaseBlockService
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * Construct
     *
     * @param ProductRepository $productRepository
     * @param EngineInterface   $templating
     */
    public function __construct(ProductRepository $productRepository, EngineInterface $templating)
    {
        parent::__construct(null, $templating);

        $this->productRepository = $productRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        // Attention: Now we gets the stub content
        return $this->renderResponse('FrontendBundle:Blocks:products.html.twig', [
        ]);
    }
}
