<?php

namespace Furniture\FrontendBundle\Blocks;

use Furniture\FrontendBundle\Repository\ProductRepository;
use Furniture\RetailerBundle\Entity\RetailerProfile;
use Sonata\BlockBundle\Block\BaseBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Furniture\FrontendBundle\Repository\Query\ProductQuery;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Furniture\UserBundle\Entity\User;

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

        $resolver->setAllowedTypes([
            'user' => User::class
        ]);
    }
    
    /**
     * {@inheritDoc}
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $settings = $blockContext->getSettings();
        
        $productQuery = new ProductQuery();
        $productQuery->withOnlyAvailable();

        $products = [];

        if ($settings['user'] instanceof User) {
            if ($settings['user']->isRetailer()) {
                $retailerProfile = $settings['user']
                    ->getRetailerUserProfile()
                    ->getRetailerProfile();

                $productQuery->withRetailer($retailerProfile);

                $products = $this->productRepository->findLatestBy($productQuery, $settings['limit']);
            }
        }
        
        return $this->renderResponse($settings['template'], [
            'products' => $products
        ]);
    }
}
