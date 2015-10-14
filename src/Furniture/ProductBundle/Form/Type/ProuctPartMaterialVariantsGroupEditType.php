<?php

namespace Furniture\ProductBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Furniture\ProductBundle\Model\ProductPartMaterialsEditFilter;
use Doctrine\ORM\EntityRepository;

class ProuctPartMaterialVariantsGroupEditType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => ProductPartMaterialsEditFilter::class,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $e) {
            $ppmFilter = $e->getData();
            $e->getForm()
                    ->add('productPartMaterialVariants', 'entity', [
                        'class' => 'Furniture\ProductBundle\Entity\ProductPartMaterialVariant',
                        'label' => false,
                        'choice_label' => 'name',
                        'expanded' => true,
                        'multiple' => true,
                        'query_builder' => function(EntityRepository $er) use ($ppmFilter) {
                            return $er
                                    ->createQueryBuilder('ppmv')
                                    ->leftJoin(
                                            'Furniture\ProductBundle\Entity\ProductPartVariantSelection', 'ppvs', 'WITH', 'ppvs.productPartMaterialVariant = ppmv.id'
                                    )
                                    ->where('ppvs.productPart = :pp')
                                    ->setParameter('pp', $ppmFilter->getProductPart())
                                    ->orderBy('ppvs.productPart', 'ASC')
                            ;
                        },
            ]);
        });
    }

    public function getName() {
        return 'prouct_part_material_variants_group_edit_type';
    }

}
