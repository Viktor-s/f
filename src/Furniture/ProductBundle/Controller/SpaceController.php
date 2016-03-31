<?php

namespace Furniture\ProductBundle\Controller;

use Furniture\ProductBundle\Entity\Space;
use Furniture\ProductBundle\Entity\Repository\SpaceRepository;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SpaceController extends ResourceController
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
        $positions = $request->get('space');

        if (null === $positions) {
            throw new NotFoundHttpException('Not found "spaces" in request.');
        }

        $em = $this->get('doctrine.orm.default_entity_manager');

        /** @var SpaceRepository $repository */
        $repository = $em->getRepository(Space::class);
        $spaces = $repository->findBy([
            'id' => array_keys($positions),
        ]);

        /** @var Space $space */
        foreach ($spaces as $space) {
            $space->setPosition($positions[$space->getId()]);
        }

        $em->flush();

        $url = $this->get('router')->generate('furniture_backend_product_space');

        $this->flashHelper->setFlash('success', 'save_position_success');

        return new RedirectResponse($url);
    }
}
