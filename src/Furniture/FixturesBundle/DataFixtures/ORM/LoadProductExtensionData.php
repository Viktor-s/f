<?php

namespace Furniture\FixturesBundle\DataFixtures\ORM;

use Sylius\Bundle\FixturesBundle\DataFixtures\DataFixture;
use \Doctrine\Common\Persistence\ObjectManager;
use \Furniture\ProductBundle\Entity\ProductExtensionOptionValue;

class LoadProductExtensionData extends DataFixture {
    
    public function getOrder() {
        return 20;
    }
    
    public function load(ObjectManager $manager) {
        $productExtension = $this->createProductExtension(
                'upholstery_material',
                [
                    $this->defaultLocale => [
                        'presentation' => 'Upholstery Material'
                    ]
                ],
                [
                    [
                        'entity' => $this->getReference('Furniture.product_extension_option.Material category'),
                        'values' => [
                            'Stamped Leather',
                            'Soft Leather',
                            'Synthetic Leather',
                            'Fabric B',
                            'Fabric A',
                        ]
                    ]
                ]
                );
        $manager->persist($productExtension);
        
        $productExtensionVariant = $this->createProductEtensionVariant('', '421 T', $productExtension, [
            'Furniture.product_extension_option.values.Fabric B'
        ]);
        $manager->persist($productExtensionVariant);
        
        $productExtensionVariant = $this->createProductEtensionVariant('', '721 T', $productExtension, [
            'Furniture.product_extension_option.values.Fabric B'
        ]);
        $manager->persist($productExtensionVariant);
        
        $productExtensionVariant = $this->createProductEtensionVariant('Synthetic Leather Brown', 'Synthetic Leather', $productExtension, [
            'Furniture.product_extension_option.values.Synthetic Leather'
        ]);
        $manager->persist($productExtensionVariant);
        
        $productExtensionVariant = $this->createProductEtensionVariant('Synthetic Leather Brown', 'Synthetic Leather', $productExtension, [
            'Furniture.product_extension_option.values.Synthetic Leather'
        ]);
        $manager->persist($productExtensionVariant);
        
        $productExtensionVariant = $this->createProductEtensionVariant('', '806 T', $productExtension, [
            'Furniture.product_extension_option.values.Fabric A'
        ]);
        $manager->persist($productExtensionVariant);
        
        $productExtensionVariant = $this->createProductEtensionVariant('', '403 T', $productExtension, [
            'Furniture.product_extension_option.values.Fabric A'
        ]);
        $manager->persist($productExtensionVariant);
        
        $manager->flush();
    }
    
    /**
     * 
     * @param string $name
     * @param array $translations
     * @param array $extension_options
     * @return \Furniture\ProductBundle\Entity\ProductExtension
     */
    protected function createProductExtension($name, array $translations, array $extension_options){
        /* @var $productExtension \Furniture\ProductBundle\Entity\ProductExtension */
        $productExtension = $this->get('furniture.repository.product_extension')->createNew();
        $productExtension->setName($name);
        
        $this->setReference( 'Furniture.product_extension.'.$name, $productExtension);
        
        foreach ($translations as $locale => $presentation) {
            $productExtension->setCurrentLocale($locale);
            $productExtension->setPresentation($presentation['presentation']);
        }
        
        foreach($extension_options as $extension_option){
            $extension_option_type = $extension_option['entity'];
            foreach($extension_option['values'] as $value){
                $extension_option_value = new ProductExtensionOptionValue();
                $extension_option_value->setValue($value);
                $extension_option_value->setOption($extension_option_type);
                $productExtension->addOptionValue($extension_option_value);
                $this->setReference('Furniture.product_extension_option.values.'.$value, $extension_option_value);
            }
        }
        return $productExtension;
    }
    
    /**
     * 
     * @param string $itemName
     * @param string $itemCode
     * @param array $productExtensionValues
     * @return \Furniture\ProductBundle\Entity\ProductExtensionVariant
     */
    protected function createProductEtensionVariant($itemName, $itemCode, $extension, array $productExtensionValues){
        /* @var $productExtensionVariant \Furniture\ProductBundle\Entity\ProductExtensionVariant */
        $productExtensionVariant = $this->get('furniture.repository.product_extension_variant')->createNew();
        $productExtensionVariant->setDescriptionalName($itemName);
        $productExtensionVariant->setDescriptionalCode($itemCode);
        $productExtensionVariant->setExtension($extension);
        
        foreach($productExtensionValues as $valueReference){
            $productExtensionVariant->addValue( $this->getReference($valueReference) );
        }
        
        return $productExtensionVariant;
    }
    
    
}

