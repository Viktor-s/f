<?php

namespace Furniture\ProductBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;

use Furniture\ProductBundle\Entity\PdpIntellectualRoot;
use Furniture\ProductBundle\Entity\PdpIntellectualCompositeExpression;
use Furniture\ProductBundle\Entity\PdpIntellectualElement;

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

    private function cacheElementsPdpPath($generator){
       $paths = $generator->getPathToElements();
       $cachedStructure = [];
       foreach($paths as $path){
           $pathStructure = [];
           foreach($path as $item){
               echo $item->getId().':'.get_class($item).'<br/>';
               if( $item instanceof PdpIntellectualRoot){
                   $pathStructure[] = [
                       'type' => 'root',
                       'id'   => $item->getId()
                   ];
               }elseif($item instanceof  PdpIntellectualCompositeExpression){
                   $pathStructure[] = [
                       'type'  => $item->getType(),
                       'id'    => $item->getId(),
                       'input_id' => $item->getPdpInput() ? $item->getPdpInput()->getId() : null,
                       'text'  => $item->getAppendText()
                   ];
               }elseif($item instanceof  PdpIntellectualElement){
                   $pathStructure[] = [
                       'type'     => 'element',
                       'id'       => $item->getId(),
                       'input_id' => $item->getInput()->getId(),
                       'text'     => $item->getInput()->getHumanName()
                   ];
                   $cachedStructure[$item->getInput()->getId()] = $pathStructure;
               }
           }
       }
       
       $pdpInput = $generator->getPdpRoot();
       $pdpInput->setTreePathForInputsJson($cachedStructure);
       
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
                $violations = $validator->validate($product, null, ['SchemesCreate']);
                if ($violations->count()) {
                    foreach ($violations as $violation) {
                        $form->addError(new FormError($violation->getMessage()));
                    }
                }
            } else {
                $form->addError(new FormError('No schemes generated.'));
            }

            if ($form->isValid() && !$product->hasVariants() && !$product->hasProductVariantsPatterns()) {
                $em->persist($pdpIntellectualRoot);
                $tree = $this->get('product.pdp_intellectual.converter')->convertToArray($pdpIntellectualRoot);                   
                $pdpIntellectualRoot->setTreeJson($tree);
                $em->flush();
                
                $this->cacheElementsPdpPath($generator);
                $em->flush();
                $toUrl = $this->generateUrl('furniture_backend_product_pdp_intellectual_index', [
                    'productId' => $product->getId(),
                ]);

                return new RedirectResponse($toUrl);
            } else {
                $form->addError(new FormError('Product already has variants or patterns.'));
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

        $form = $this->createForm(new PdpIntellectualRootType(), $newPdpIntellectual, ['graph_tree' => $tree]);

        $form->handleRequest($request);

        if ($request->getMethod() === Request::METHOD_POST) {
            $em = $this->get('doctrine.orm.default_entity_manager');

            $treeData = $form->get('tree')->getData();
            $treeData = json_decode($treeData, true);

            $this->get('product.pdp_intellectual.creator')->createFromArray($newPdpIntellectual, $treeData);
            
            $validator = $this->get('validator');
            $generator = $this->get('furniture.generator.pdp_product_scheme');
            $generator->setPdpRoot($newPdpIntellectual);
            $generator->generate();
            $schemes = $generator->getProductSchemes();

            if ($schemes) {
                $productSchemes = $product->getProductSchemes();

                foreach ($productSchemes as $pScheme) {
                    $pSchemeParts = [];
                    foreach ($pScheme->getProductParts() as $part) {
                        $pSchemeParts[] = $part->getId();
                    }

                    sort($pSchemeParts);
                    $pHash = implode($pSchemeParts);

                    $has = false;
                    foreach ($schemes as $scheme) {
                        $schemeParts = [];
                        foreach ($scheme->getProductParts() as $part) {
                            $schemeParts[] = $part->getId();
                        }

                        sort($schemeParts);
                        $hash = implode($schemeParts);

                        if ($hash === $pHash) {
                            $has = true;
                            $schemes->removeElement($scheme);
                            break;
                        }
                    }

                    if (!$has) {
                        $productSchemes->removeElement($pScheme);
                        $em->remove($pScheme);
                    }
                }

                $schemes = new ArrayCollection(array_merge($productSchemes->toArray(), $schemes->toArray()));
                $product->setProductSchemes($schemes);

                $violations = $validator->validate($product, null, ['SchemesCreate']);

                if ($violations->count()) {
                    foreach ($violations as $violation) {
                        $form->addError(new FormError($violation->getMessage()));
                    }
                }
            } else {
                $form->addError(new FormError('No schemes generated.'));
            }

            if ($form->isValid() && !$product->hasVariants() && !$product->hasProductVariantsPatterns()) {
                // We use transactional because we must remove and add new element.
                $em->transactional(function () use ($pdpIntellectualRoot, $newPdpIntellectual, $em, $generator) {
                    $em->remove($pdpIntellectualRoot);
                    $em->persist($newPdpIntellectual);
                    
                    $tree = $this->get('product.pdp_intellectual.converter')->convertToArray($newPdpIntellectual);
                    $newPdpIntellectual->setTreeJson($tree);
                    $this->cacheElementsPdpPath($generator);
                });
                
                $toUrl = $this->generateUrl('furniture_backend_product_pdp_intellectual_index', [
                    'productId' => $product->getId(),
                ]);

                return new RedirectResponse($toUrl);
            } else {
                $form->addError(new FormError('Product already has variants or patterns.'));
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
