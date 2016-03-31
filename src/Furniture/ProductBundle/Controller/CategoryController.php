<?php

namespace Furniture\ProductBundle\Controller;

use Furniture\ProductBundle\Entity\Category;
use Furniture\ProductBundle\Entity\Repository\CategoryRepository;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CategoryController extends ResourceController
{
    /**
     * Sort positions
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function sortPositionsAction(Request $request)
    {
        $positions = $request->get('category');

        if (null === $positions) {
            throw new NotFoundHttpException('Not found "categories" in request.');
        }

        $em = $this->get('doctrine.orm.default_entity_manager');

        /** @var CategoryRepository $repository */
        $repository = $em->getRepository(Category::class);
        $categories = $repository->findBy([
            'id' => array_keys($positions),
        ]);

        /** @var Category $category */
        foreach ($categories as $category) {
            $category->setPosition($positions[$category->getId()]);
        }

        $em->flush();

        $url = $this->get('router')->generate('furniture_backend_product_category');

        $this->flashHelper->setFlash('success', 'save_position_success');

        return new RedirectResponse($url);
    }
}
