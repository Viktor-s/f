<?php

namespace Furniture\ProductBundle\Controller;

use Furniture\ProductBundle\Entity\Type;
use Furniture\ProductBundle\Entity\Repository\TypeRepository;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TypeController extends ResourceController
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
        $positions = $request->get('type');

        if (null === $positions) {
            throw new NotFoundHttpException('Not found "types" in request.');
        }

        $em = $this->get('doctrine.orm.default_entity_manager');

        /** @var TypeRepository $repository */
        $repository = $em->getRepository(Type::class);
        $types = $repository->findBy([
            'id' => array_keys($positions),
        ]);

        /** @var Type $type */
        foreach ($types as $type) {
            $type->setPosition($positions[$type->getId()]);
        }

        $em->flush();

        $url = $this->get('router')->generate('furniture_backend_product_type');

        $this->flashHelper->setFlash('success', 'save_position_success');

        return new RedirectResponse($url);
    }
}
