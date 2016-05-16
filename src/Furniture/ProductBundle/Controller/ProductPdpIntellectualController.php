<?php

namespace Furniture\ProductBundle\Controller;

use Furniture\ProductBundle\Entity\PdpIntellectualRoot;
use Furniture\ProductBundle\Entity\Product;
use Furniture\ProductBundle\Form\Type\PdpIntellectual\PdpIntellectualRootType;
use Furniture\ProductBundle\Generator\PdpIntelligentSchemesGenerator;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductPdpIntellectualController extends ResourceController
{
    /**
     * {@inheritDoc}
     */
    public function indexAction(Request $request)
    {
        $this->isGrantedOr403('index');

        $criteria = $this->config->getCriteria();
        $sorting = $this->config->getSorting();

        $repository = $this->getRepository();
        $product = $this->loadProduct($request);

        $criteria['product'] = $product;

        $resources = $this->resourceResolver->getResource(
            $repository,
            'findBy',
            [$criteria, $sorting, $this->config->getLimit()]
        );

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('index.html'))
            ->setTemplateVar($this->config->getPluralResourceName())
            ->setData($resources)
            ->setTemplateData([
                'product' => $product,
                'items'   => $resources,
            ]);

        return $this->handleView($view);
    }

    /**
     * {@inheritDoc}
     */
    public function createAction(Request $request)
    {
        $this->isGrantedOr403('create');
        $product = $this->loadProduct($request);

        $pdpIntellectualRoot = new PdpIntellectualRoot();
        $pdpIntellectualRoot->setProduct($product);

        $form = $this->createForm(new PdpIntellectualRootType(), $pdpIntellectualRoot);

        $form->add('tree', 'textarea', [
            'mapped'   => false,
            'required' => true,
        ]);

        if ($request->getMethod() === Request::METHOD_POST) {
            $form->submit($request);

            $treeData = $form->get('tree')->getData();
            $treeData = json_decode($treeData, true);
            $this->get('product.pdp_intellectual.creator')->createFromArray($pdpIntellectualRoot, $treeData);
            $em = $this->get('doctrine.orm.default_entity_manager');
            $validator = $this->get('validator');
            $generator = $this->get('furniture.generator.pdp_product_scheme');
            $generator->setPdpRoot($pdpIntellectualRoot);
            $generator->generate();
            $schemes = $generator->getProductSchemes();

            if ($schemes) {
                $product->setProductSchemes($schemes);
                $violations = $validator->validate($product);
                if ($violations->count()) {
                    foreach ($violations as $violation) {
                        $form->addError(new FormError($violation->getMessage()));
                    }
                }
            } else {
                $form->addError(new FormError('No schemes generated.'));
            }

            if ($form->isValid()) {
                $em->persist($pdpIntellectualRoot);
                $em->flush();

                $toUrl = $this->generateUrl('furniture_backend_product_pdp_intellectual_index', [
                    'productId' => $product->getId(),
                ]);

                return new RedirectResponse($toUrl);
            }
        }

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('create.html'))
            ->setTemplateData([
                'product' => $product,
                'form'    => $form->createView(),
            ]);

        return $this->handleView($view);
    }

    /**
     * {@inheritDoc}
     */
    public function updateAction(Request $request)
    {
        $this->isGrantedOr403('update');

        /** @var PdpIntellectualRoot $pdpIntellectualRoot */
        $pdpIntellectualRoot = $this->findOr404($request);
        $product = $this->loadProduct($request);
        $tree = $this->get('product.pdp_intellectual.converter')->convertToArray($pdpIntellectualRoot);

        $newPdpIntellectual = new PdpIntellectualRoot();
        $newPdpIntellectual->setProduct($product);
        $newPdpIntellectual->setName($pdpIntellectualRoot->getName());
        $newPdpIntellectual->setGraphJson($pdpIntellectualRoot->getGraphJson());

        $form = $this->createForm(new PdpIntellectualRootType(), $newPdpIntellectual);

        $form->add('tree', 'textarea', [
            'mapped' => false,
            'data'   => json_encode($tree, JSON_UNESCAPED_UNICODE),
        ]);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->get('doctrine.orm.default_entity_manager');

            $treeData = $form->get('tree')->getData();
            $treeData = json_decode($treeData, true);

            $this->get('product.pdp_intellectual.creator')->createFromArray($newPdpIntellectual, $treeData);

            $validator = $this->get('validator');
            $generator = $this->get('furniture.generator.pdp_product_scheme');
            $generator->setPdpRoot($pdpIntellectualRoot);
            $generator->generate();
            $schemes = $generator->getProductSchemes();

            if ($schemes) {
                $product->setProductSchemes($schemes);
                $violations = $validator->validate($product);
                if ($violations->count()) {
                    foreach ($violations as $violation) {
                        $form->addError(new FormError($violation->getMessage()));
                    }
                }
            } else {
                $form->addError(new FormError('No schemes generated.'));
            }

            if ($form->isValid()) {
                // We use transactional because we must remove and add new element.
                $em->transactional(function () use ($pdpIntellectualRoot, $newPdpIntellectual, $em) {
                    $em->remove($pdpIntellectualRoot);
                    $em->persist($newPdpIntellectual);
                });

                $toUrl = $this->generateUrl('furniture_backend_product_pdp_intellectual_index', [
                    'productId' => $product->getId(),
                ]);

                return new RedirectResponse($toUrl);
            }
        }

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('update.html'))
            ->setTemplateData([
                'product'          => $product,
                'pdp_intellectual' => $pdpIntellectualRoot,
                'form'    => $form->createView(),
            ]);

        return $this->handleView($view);
    }

    /**
     * {@inheritDoc}
     */
    public function deleteAction(Request $request)
    {
        $product = $this->loadProduct($request);

        $this->isGrantedOr403('delete');
        $this->domainManager->delete($this->findOr404($request));

        $toUrl = $this->generateUrl('furniture_backend_product_pdp_intellectual_index', [
            'productId' => $product->getId(),
        ]);

        return new RedirectResponse($toUrl);
    }

    /**
     * Load product
     *
     * @param Request $request
     *
     * @return Product
     */
    private function loadProduct(Request $request)
    {
        $productId = $request->get('productId');

        if (!$productId) {
            throw new NotFoundHttpException('Missing "productId" parameter.');
        }

        $product = $this->get('doctrine.orm.default_entity_manager')
            ->find(Product::class, $productId);

        if (!$product) {
            throw new NotFoundHttpException(sprintf(
                'Not found product with identifier "%s".',
                $productId
            ));
        }

        return $product;
    }
}
