<?php

namespace Furniture\ProductBundle\Controller;

use Furniture\ProductBundle\Entity\Style;
use Furniture\ProductBundle\Entity\Repository\StyleRepository;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class StyleController extends ResourceController
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
        $positions = $request->get('style');

        if (null === $positions) {
            throw new NotFoundHttpException('Not found "styles" in request.');
        }

        $em = $this->get('doctrine.orm.default_entity_manager');

        /** @var StyleRepository $repository */
        $repository = $em->getRepository(Style::class);
        $styles = $repository->findBy([
            'id' => array_keys($positions),
        ]);

        /** @var Style $style */
        foreach ($styles as $style) {
            $style->setPosition($positions[$style->getId()]);
        }

        $em->flush();

        $url = $this->get('router')->generate('furniture_backend_product_style');

        $this->flashHelper->setFlash('success', 'save_position_success');

        return new RedirectResponse($url);
    }
}
