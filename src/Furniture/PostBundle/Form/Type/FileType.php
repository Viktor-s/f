<?php
namespace Furniture\PostBundle\Form\Type;

use Sylius\Bundle\CoreBundle\Form\Type\ImageType as BaseImageType;
use Symfony\Component\Form\FormBuilderInterface;

class FileType extends BaseImageType 
{
    
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->add('name', 'text');
    }
    
}

