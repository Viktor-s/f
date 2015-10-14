<?php

namespace Furniture\ProductBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Doctrine\ORM\EntityRepository;

use Furniture\ProductBundle\Entity\Product;
use Furniture\ProductBundle\Model\GroupVaraintFiler;
use Sylius\Component\Product\Model\OptionValue;
use Furniture\SkuOptionBundle\Entity\SkuOptionVariant;
use Furniture\ProductBundle\Entity\ProductPartMaterialVariant;

class GroupVariantFilterType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => GroupVaraintFiler::class,
        ));
    }
    
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $variantFilter = $builder->getData();
        
        $builder
                ->add('OptionValues', 'entity', [
            'class' => OptionValue::class,
            'label' => false,
            'expanded' => true,
            'multiple' => true,
            'query_builder' => function(EntityRepository $er ) use ($variantFilter) {
                    return $er
                                    ->createQueryBuilder('ov')
                                    ->where('ov.option in (:oids)')
                                    ->setParameter('oids', $variantFilter->getProduct()->getOptions())
                                    ->orderBy('ov.option', 'ASC')
                    ;
                },
        ])
                ->add('skuOptionsValues', 'entity', [
            'class' => SkuOptionVariant::class,
            'choice_label' => 'value',
            'label' => false,
            'expanded' => true,
            'multiple' => true,
            'query_builder' => function(EntityRepository $er) use ($variantFilter) {
                    return $er
                                    ->createQueryBuilder('sov')
                                    ->where('sov.product = :soproduct')
                                    ->setParameter('soproduct', $variantFilter->getProduct())
                                    ->orderBy('sov.skuOptionType', 'ASC')
                    ;
                },
        ])
            ->add('productPartMaterialVariants', 'collection', [
                'type' => new ProuctPartMaterialVariantsGroupEditType(),
                'label' => false,
                'allow_add' => false,
                ]);
    }
    
    public function getName() {
        return 'group_variant_filter_type';
    }

}

