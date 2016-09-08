<?php

namespace Furniture\FrontendBundle\Blocks;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\FrontendBundle\Repository\ProductRepository;
use Furniture\ProductBundle\Entity\BestSellersSet;
use Sonata\BlockBundle\Block\BaseBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Furniture\UserBundle\Entity\User;

class ProductsBestSellersBlock extends BaseBlockService
{
    /**
     * @var ProductRepository
     */
    private $em;

    /**
     * Construct
     *
     * @param EntityManagerInterface $em
     * @param EngineInterface   $templating
     */
    public function __construct(EntityManagerInterface $em, EngineInterface $templating)
    {
        parent::__construct(null, $templating);

        $this->em = $em;
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultSettings(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'limit' => 15,
            'user' => null,
            'template' => 'FrontendBundle:Blocks:products_best_sellers.html.twig'
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
        $products = [];
        $settings = $blockContext->getSettings();

        $repository = $this->em->getRepository(BestSellersSet::class);
        /** @var BestSellersSet[] $bestSellersSet */
        $bestSellersSet = $repository->findBy(['active' => true], null, ['limit' => $settings['limit']]);

        if ($bestSellersSet) {
            $products = $bestSellersSet[0]->getProducts();
        }
        
        return $this->renderResponse($settings['template'], [
            'products' => $products
        ]);
    }
}
