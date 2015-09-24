<?php

namespace Furniture\FrontendBundle\Controller;

use Furniture\FrontendBundle\Repository\ProductRepository;
use Furniture\FrontendBundle\Repository\SpecificationRepository;
use Furniture\ProductBundle\Entity\ProductVariant;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ProductController
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var SpecificationRepository
     */
    private $specificationRepository;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * Construct
     *
     * @param \Twig_Environment       $twig
     * @param ProductRepository       $productRepository
     * @param SpecificationRepository $specificationRepository
     * @param TokenStorageInterface   $tokenStorage
     */
    public function __construct(
        \Twig_Environment $twig,
        ProductRepository $productRepository,
        SpecificationRepository $specificationRepository,
        TokenStorageInterface $tokenStorage
    ) {
        $this->twig = $twig;
        $this->productRepository = $productRepository;
        $this->specificationRepository = $specificationRepository;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Show product action
     *
     * @param Request $request
     * @param int     $product
     *
     * @return Response
     */
    public function product(Request $request, $product)
    {
        $user = $this->tokenStorage->getToken()
            ->getUser();

        $product = $this->productRepository->find($productId = $product);

        if (!$product) {
            throw new NotFoundHttpException(sprintf(
                'Not found product with identifier "%s".',
                $productId
            ));
        }

        // Group options, sku options and extensions and min and max price
        $variants = [];
        $options = [];

        /** @var \Furniture\ProductBundle\Entity\ProductVariant $variant */
        foreach ($product->getVariants() as $variant) {
            $hash = '';

            foreach ($variant->getOptions() as $option) {
                $key = 'o' . $option->getName();

                if (!isset($options[$key])) {
                    $options[$key] = [
                        'name' => $option->getName(),
                        'values' => []
                    ];
                }

                $value = $option->getValue();
                $options[$key]['values'][$value] = $value;
                $hash .= $value;
            }

            /** @var \Furniture\SkuOptionBundle\Entity\SkuOptionVariant $option */
            foreach ($variant->getSkuOptions() as $option) {
                $key = 'so' . $option->getName();

                if (!isset($options[$key])) {
                    $options[$key] = [
                        'name' => $option->getName(),
                        'values' => []
                    ];
                }

                $value = $option->getValue();
                $options[$key]['values'][$value] = $value;
                $hash .= $value;
            }

            $variants[$hash] = [
                'sku' => $variant->getSku(),
                'price' => $variant->getPrice()
            ];
        }

        // Get min and max prices
        $pricesMap = [];

        $product->getVariants()->forAll(function ($key, ProductVariant $productVariant) use (&$pricesMap) {
            $pricesMap[] = $productVariant->getPrice();

            return true;
        });

        $prices = [
            'min' => min($pricesMap),
            'max' => max($pricesMap)
        ];

        $specifications = $this->specificationRepository->findForUser($user);

        $content = $this->twig->render('FrontendBundle:Product:product.html.twig', [
            'product' => $product,
            'options' => $options,
            'variants' => $variants,
            'prices' => $prices,
            'specifications' => $specifications
        ]);

        return new Response($content);
    }
}
