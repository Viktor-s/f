<?php

namespace Furniture\FrontendBundle\Blocks;

use Furniture\FrontendBundle\Repository\ProductRepository;
use Sonata\BlockBundle\Block\BaseBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Furniture\FrontendBundle\Repository\Query\ProductQuery;
use Furniture\CommonBundle\Entity\User;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

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
    public function setDefaultSettings(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'limit' => 5,
            'user' => null,
            'template' => 'FrontendBundle:Blocks:products.html.twig'
        ]);
    }
    
    /**
     * {@inheritDoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $settings = $blockContext->getSettings();
        
        $pQuery = new ProductQuery();
        $pQuery->withOnlyAvailable();
        if($settings['user'] instanceof User)
            $pQuery->withContentUser($settings['user']);
        
        $products = $this->productRepository->fundLatestBy($pQuery, $settings['limit']);
        
        return $this->renderResponse($settings['template'], [
            'products' => $products
        ]);
    }
}
