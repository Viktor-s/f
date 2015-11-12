<?php

namespace Furniture\CommonBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BackendImageType extends AbstractType
{
    /**
     * @var string
     */
    private $class;

    /**
     * Construct
     *
     * @param string $class
     */
    public function __construct($class)
    {
        $this->class = $class;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => $this->class,
            'filter' => 's100x100',
            'allow_delete' => true,
            'width' => 100,
            'height' => 100
        ]);

        $resolver->setAllowedTypes('filter', ['null', 'string']);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('path', 'hidden')
            ->add('file', 'file', array(
                'label' => 'sylius.form.image.file'
            ));
    }

    /**
     * {@inheritDoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $path = null;

        /** @var \Sylius\Component\Core\Model\Image $image */
        if ($image = $form->getData()) {
            $path = $image->getPath();
        }

        $view->vars = array_merge([
            'path' => $path,
            'filter' => $options['filter'],
            'allow_delete' => $options['allow_delete'],
            'width' => 100,
            'height' => 100
        ], $view->vars);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'backend_image';
    }
}
