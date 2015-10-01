<?php

namespace Furniture\ProductBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\Common\Collections\Collection;

use Furniture\ProductBundle\Entity\ProductPartVariantSelection;

class ProductVariantPartMaterialsType extends AbstractType
{
    
    /**
     * @var EntityManagerInterface
     */
    private $em;
    
    /**
     * Construct
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired([
            'product_varant_object'
        ]);
        $resolver->setDefaults([
            'data_class' => Collection::class,
        ]);
    }
    
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /**
         * @var $productVariant \Furniture\ProductBundle\Entity\ProductVariant
         */
        $productVariant = $options['product_varant_object'];
        
        $contains = [];
        foreach($productVariant->getProductPartVariantSelections() as $ppvs )
        {
            $contains[$ppvs->getProductPartMaterialVariant()->getId()] = $ppvs->getProductPartMaterialVariant();
        }
        
        foreach( $productVariant->getProduct()->getProductParts() as $productPart )
        {
            $productPartMaterialsVariants = [];
            $selected = null;
            foreach($productPart->getProductPartMaterials() as $productPartMaterial)
            {
                foreach($productPartMaterial->getVariants() as $productPartMaterialVariant)
                $productPartMaterialVariants[] = $productPartMaterialVariant;
                if( isset($contains[$productPartMaterialVariant->getId()])){
                    $selected = $productPartMaterialVariant;
                }
            }
            
            if(count($productPartMaterialsVariants) > 0)
            {
                $builder->add( $i, 'entity', [
                    'class' => 'Furniture\ProductBundle\Entity\ProductPartMaterialVariant',
                    'choice_label' => 'name',
                    'label' => $productPart->getLabel(),
                    'choices' => $productPartMaterialVariants,
                    'data' => $selected,
                ])->addModelTransformer(new SpecificationIdModelTransformer($this->em));
            }
        }
        
        //$builder->get('specification')->addModelTransformer(new SpecificationIdModelTransformer($this->em));
    }
    
    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'ProductVariantPartMaterialsType';
    }
    
}

