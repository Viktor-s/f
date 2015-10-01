<?php

namespace Furniture\ProductBundle\Form\DataTransformer;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Furniture\ProductBundle\Entity\ProductVariant;
use Furniture\ProductBundle\Entity\ProductPart;
use Furniture\ProductBundle\Entity\ProductPartMaterialVariant;
use Furniture\ProductBundle\Entity\ProductPartVariantSelection;
use Doctrine\Common\Collections\ArrayCollection;

class ProductPartVariantMaterialVariantSelectionTransformer implements DataTransformerInterface
{
    /**
     *
     * @var \Furniture\ProductBundle\Entity\ProductVariant
     */
    protected $productVariant;
    
    /**
     *
     * @var \Doctrine\ORM\EntityManagerInterface 
     */
    protected $em;
    
    /**
     * 
     * @param ProductVariant $productVariant
     */
    function __construct(ProductVariant $productVariant, EntityManagerInterface $em) {
        $this->productVariant = $productVariant;
        $this->em = $em;
    }
    
    /**
     * Transforms an object (issue) to a string (number).
     *
     * @param  Issue|null $issue
     * @return string
     */
    public function transform($value)
    {
        return $value;
    }

    /**
     * 
     * @param \Doctrine\Common\Collections\ArrayCollection $value
     */
    public function reverseTransform($values) {
        
        $collection = new ArrayCollection();
        
        foreach($values as $value)
        {
            list($productPartId, $productPartMaterialVariantId) = explode( '_', $value );
            
            $value = $this->em->getRepository(ProductPartVariantSelection::class)
                    ->findBy([
                        'productPart' => $productPartId,
                        'productVariant' => $this->productVariant->getId(),
                        'productPartMaterialVariant' => $productPartMaterialVariantId
                    ]);

            if(!$value){
                $value = new ProductPartVariantSelection();
                $value->setProductPart($this->em->getRepository(ProductPart::class)->find($productPartId));
                $value->setProductPartMaterialVariant($this->em->getRepository(ProductPartMaterialVariant::class)->find($productPartMaterialVariantId));
            }
            
            $collection->add($value);
        }
        return $collection;
    }

}