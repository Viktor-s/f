<?php

namespace Furniture\ProductBundle\Controller;

use Furniture\ProductBundle\Entity\BestSellersSet;
use Furniture\ProductBundle\Entity\PdpIntellectualRoot;
use Furniture\ProductBundle\Entity\PdpIntellectualCompositeExpression;
use Furniture\ProductBundle\Entity\PdpIntellectualElement;
use Furniture\ProductBundle\Form\Type\BestSellersSetType;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class ProductBestSellersController extends ResourceController
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
//                'product' => $product,
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

        $bestSellersSet = new BestSellersSet();

        $form = $this->createForm(new BestSellersSetType(), $bestSellersSet);

        if ($request->getMethod() === Request::METHOD_POST) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $em = $this->get('doctrine.orm.default_entity_manager');
                $em->persist($bestSellersSet);

                foreach ($bestSellersSet->getBestSellers() as $bestSeller) {
                    $bestSeller->setBestSellerSet($bestSellersSet);
                }

                $em->flush();
                $toUrl = $this->generateUrl('furniture_backend_product_best_sellers_index');

                return new RedirectResponse($toUrl);
            }
        }

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('create.html'))
            ->setTemplateData([
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

        /** @var BestSellersSet $bestSellersSet */
        $bestSellersSet = $this->findOr404($request);
        $form = $this->createForm(new BestSellersSetType(), $bestSellersSet);

        if ($request->getMethod() === Request::METHOD_POST) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $em = $this->get('doctrine.orm.default_entity_manager');

                foreach ($bestSellersSet->getBestSellers() as $bestSeller) {
                    $bestSeller->setBestSellerSet($bestSellersSet);
                }

                $this->getRepository()->updateActiveSet($bestSellersSet->getId());

                $em->flush();
                $toUrl = $this->generateUrl('furniture_backend_product_best_sellers_index');

                return new RedirectResponse($toUrl);
            }
        }

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('update.html'))
            ->setTemplateData([
                'bestSellersSet'          => $bestSellersSet,
                'form'    => $form->createView(),
            ]);

        return $this->handleView($view);
    }

    /**
     * {@inheritDoc}
     */
    public function deleteAction(Request $request)
    {
        /** @var BestSellersSet $bestSellersSet */
        $bestSellersSet = $this->findOr404($request);
        $this->isGrantedOr403('delete');

        $this->domainManager->delete($bestSellersSet);

        $toUrl = $this->generateUrl('furniture_backend_product_best_sellers_index');

        return new RedirectResponse($toUrl);
    }
}
