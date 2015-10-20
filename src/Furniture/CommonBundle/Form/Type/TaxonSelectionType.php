<?php

namespace Furniture\CommonBundle\Form\Type;

use Sylius\Bundle\TaxonomyBundle\Form\Type\TaxonSelectionType as BaseTaxonSelectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TaxonSelectionType extends BaseTaxonSelectionType
{
    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setDefaults([
            'expanded' => false
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $taxonomies = $this->taxonomyRepository->findAll();

        $builder->addModelTransformer(new $options['model_transformer']['class']($taxonomies, $options['model_transformer']['save_objects']));

        foreach ($taxonomies as $taxonomy) {
            /* @var $taxonomy \Sylius\Component\Taxonomy\Model\TaxonomyInterface */
            $taxons = $this->taxonRepository->getTaxonsAsList($taxonomy);
            $fixedTaxons = [];

            foreach ($taxons as $taxon) {
                $fixedTaxons[$taxon->getId()] = $taxon;
            }

            $builder->add($taxonomy->getId(), 'choice', array(
                'choice_list' => new ObjectChoiceList($fixedTaxons, null, array(), null, 'id'),
                'multiple'    => $options['multiple'],
                'expanded'    => $options['expanded'],
                'label'       => $taxonomy->getName(),
            ));
        }
    }
}
