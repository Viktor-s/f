<?php

namespace Furniture\FixturesBundle\DataFixtures\ORM;

use Sylius\Bundle\FixturesBundle\DataFixtures\DataFixture;
use \Doctrine\Common\Persistence\ObjectManager;
use \Doctrine\Common\Collections\ArrayCollection;

class LoadCompositiesData extends DataFixture {
    
    public function getOrder() {
        return 51;
    }
    
    public function load(ObjectManager $manager)
    {
        // We have a error after migrate to own categorization
        return;
        for ($i = 1; $i <= 5; $i++) {
            $compositeTemplate = $this->createRandomCompositeTemplate();
            $manager->persist($compositeTemplate);
            $manager->flush();
            for( $i = 1; $i <= rand(1, 5); $i++ ){
                $composite = $this->createRandomComposite($compositeTemplate);
                $manager->persist($composite);
            }
            
        }
        
        $manager->flush();
    }
    
    /**
     * 
     * @param \Furniture\CompositionBundle\Entity\CompositeTemplate $compositeTemplate
     * @return \Furniture\CompositionBundle\Entity\Composite
     */
    protected function createRandomComposite(\Furniture\CompositionBundle\Entity\CompositeTemplate $compositeTemplate){
        /* @var $composite \Furniture\CompositionBundle\Entity\Composite */
        $composite = $this->get('furniture.repository.composite')->createNew();
        $composite->setName(implode(' ', $this->faker->words(rand(1, 3))));
        $composite->setTemplate($compositeTemplate);        
        
        foreach($compositeTemplate->getItems() as $item){
            $itemValidProducts = $this->getProductsByCompositeCollectionTaxon( $compositeTemplate->getCollection(), $item->getTaxon());
         
            for ($i = 1; $i <= $item->getCount(); $i++) {
                if($validProduct = $this->faker->randomElement($itemValidProducts)){
                    $compositeProduct = new \Furniture\CompositionBundle\Entity\CompositeProduct();
                    $compositeProduct->setComposite($composite);
                    $compositeProduct->setProduct($validProduct);
                    $compositeProduct->setTemplateItem($item);
                    $composite->getProducts()->add($compositeProduct);
                }
            }
        }
        
        return $composite;
    }
    
    /**
     * 
     * @return \Furniture\CompositionBundle\Entity\CompositeTemplate
     */
    protected function createRandomCompositeTemplate(){
        /* @var $template \Furniture\CompositionBundle\Entity\CompositeTemplate */
        $template = $this->get('furniture.repository.composite_template')->createNew();
        $template->setName(implode(' ', $this->faker->words(rand(1, 3))));
        $template->setCollection(
                $this->getReference('Furniture.composite_collection.'.$this->faker->randomElement([
            'Mitchell',
            'Gallery',
            'Takat',
            'Holtom',
            'Natura',
            'Ashworth Customizable Desk System'
            ]))
                );
        $templateItems = [];
        $products = $this->getProductsByCompositeCollection($template->getCollection());
        
        for ($i = 1; $i <= rand(2,10); $i++) {
            $product = $this->faker->randomElement($products);
            $taxon = $product->getTaxons()->first();
            if(!isset($templateItems[$taxon->getId()])){
                $templateItems[$taxon->getId()] = new \Furniture\CompositionBundle\Entity\CompositeTemplateItem();
                $templateItems[$taxon->getId()]->setTemplate($template);
                $templateItems[$taxon->getId()]->setTaxon($taxon);
                $templateItems[$taxon->getId()]->setCount(1);
            }
            $templateItems[$taxon->getId()]->setCount( ($templateItems[$taxon->getId()]->getCount()+1) );
        }
        
        $template->setItems(new ArrayCollection($templateItems));
        
        return $template;
    }
    
    protected function getProductsByCompositeCollection($collection){
        return $this->getProductRepository()->createQueryBuilder('p')
                ->leftJoin('Furniture\CompositionBundle\Entity\CompositeCollection', 'compositeCollections')
                ->where('compositeCollections = :collection')->setParameter('collection', $collection)
                ->getQuery()->getResult();
    }
    
    protected function getProductsByCompositeCollectionTaxon($collection, $taxon){
        return $this->getProductRepository()->createQueryBuilder('p')
                ->leftJoin('p.compositeCollections', 'compositeCollections')
                ->leftJoin('p.taxons', 'taxons')
                ->where('compositeCollections = :collection AND taxons = :taxon')
                    ->setParameter('collection', $collection)
                    ->setParameter('taxon', $taxon)
                ->getQuery()->getResult();
    }
    
}
