<?php

namespace Furniture\ProductBundle\Controller;

use Furniture\ProductBundle\Entity\Readiness;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ReadinessController extends ResourceController
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
        $positions = $request->get('readiness');

        if (null === $positions) {
            throw new NotFoundHttpException('Not found "readiness" in request.');
        }

        $em = $this->get('doctrine.orm.default_entity_manager');

        $repository = $em->getRepository(Readiness::class);
        $readiness = $repository->findBy([
            'id' => array_keys($positions)
        ]);

        foreach ($readiness as $readines) {
            $readines->setPosition($positions[$readines->getId()]);
        }

        $em->flush();

        $url = $this->get('router')->generate('furniture_backend_product_readiness_index');

        $this->flashHelper->setFlash('success', 'save_position_success');

        return new RedirectResponse($url);
    }
}
