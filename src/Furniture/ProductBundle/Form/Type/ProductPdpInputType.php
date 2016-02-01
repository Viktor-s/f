<?php

namespace Furniture\ProductBundle\Form\Type;

use Furniture\ProductBundle\Entity\ProductPdpInput;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductPdpInputType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProductPdpInput::class,
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('position', 'hidden', [
                'label' => 'Position',
            ])
            ->add('type', 'choice', [
                'label'    => 'Input type',
                'required' => true,
                'choices'  => [
                    ProductPdpInput::SELECT_DEFAULT_TYPE => 'Default',
                    ProductPdpInput::SELECT_INLINE_TYPE  => 'Inline',
                    ProductPdpInput::SELECT_POPUP_TYPE   => 'Popup',
                ],
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $data = $form->getData();

        if (!$data) {
            throw new \RuntimeException('Can not build view without data.');
        }

        $view->vars = array_merge($view->vars, [
            'pdp_input' => $data,
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'product_pdp_input';
    }
}
