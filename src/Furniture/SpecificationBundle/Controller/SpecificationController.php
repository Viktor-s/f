<?php

namespace Furniture\SpecificationBundle\Controller;

use Furniture\SpecificationBundle\Entity\SpecificationItem;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class SpecificationController extends ResourceController
{
    /**
     * {@inheritDoc}
     */
    public function indexAction(Request $request)
    {
        $filters = $this->get('doctrine.orm.default_entity_manager')->getFilters();

        if ($filters->isEnabled('softdeleteable')) {
            $filters->disable('softdeleteable');
        }

        return parent::indexAction($request);
    }

    /**
     * Remove specification item
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteItemAction(Request $request)
    {
        $em = $this->get('doctrine.orm.default_entity_manager');

        $item = $em->find(SpecificationItem::class, $request->get('id'));

        if (!$item) {
            throw new NotFoundHttpException(sprintf(
                'Not found specification item with identifier "%s".',
                $request->get('id')
            ));
        }

        if (!$this->get('sylius.authorization_checker')->isGranted('furniture.specification.delete')) {
            throw new AccessDeniedException();
        }

        $specificationId = $item->getSpecification()->getId();

        $em->remove($item);
        $em->flush();

        $this->flashHelper->setFlash('info', 'Success remove item');

        $url = $this->generateUrl('furniture_backend_specification_show', [
            'id' => $specificationId
        ]);

        return new RedirectResponse($url);
    }
}
