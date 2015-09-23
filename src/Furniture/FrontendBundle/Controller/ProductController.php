<?php

namespace Furniture\FrontendBundle\Controller;

use Furniture\FrontendBundle\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
     * Construct
     *
     * @param \Twig_Environment $twig
     * @param ProductRepository $productRepository
     */
    public function __construct(\Twig_Environment $twig, ProductRepository $productRepository)
    {
        $this->twig = $twig;
        $this->productRepository = $productRepository;
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
        $product = $this->productRepository->find($productId = $product);

        if (!$product) {
            throw new NotFoundHttpException(sprintf(
                'Not found product with identifier "%s".',
                $productId
            ));
        }

        $content = $this->twig->render('FrontendBundle:Product:show.html.twig', [
            'product' => $product
        ]);

        return new Response($content);
    }
}
