<?php

namespace Furniture\FixturesBundle\DataFixtures\ORM;

use Sylius\Bundle\FixturesBundle\DataFixtures\DataFixture;
use \Doctrine\Common\Persistence\ObjectManager;
use \Furniture\ProductBundle\Entity\ProductPartMaterialOptionValue;

class LoadProductPartMaterialData extends DataFixture {
    
    public function getOrder() {
        return 20;
    }
    
    public function load(ObjectManager $manager) {
        $roductPartMaterial = $this->createProductPartMaterial(
                'upholstery_material',
                [
                    $this->defaultLocale => [
                        'presentation' => 'Upholstery Material'
                    ]
                ],
                [
                    [
                        'entity' => $this->getReference('Furniture.product_part_material_option.Material category'),
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
        $manager->persist($roductPartMaterial);
        
        $roductPartMaterialVariant = $this->createProductEtensionVariant('', '421 T', $roductPartMaterial, [
            'Furniture.product_part_material_option.values.Fabric B'
        ]);
        $manager->persist($roductPartMaterialVariant);
        
        $roductPartMaterialVariant = $this->createProductEtensionVariant('', '721 T', $roductPartMaterial, [
            'Furniture.product_part_material_option.values.Fabric B'
        ]);
        $manager->persist($roductPartMaterialVariant);
        
        $roductPartMaterialVariant = $this->createProductEtensionVariant('Synthetic Leather Brown', 'Synthetic Leather', $roductPartMaterial, [
            'Furniture.product_part_material_option.values.Synthetic Leather'
        ]);
        $manager->persist($roductPartMaterialVariant);
        
        $roductPartMaterialVariant = $this->createProductEtensionVariant('', '806 T', $roductPartMaterial, [
            'Furniture.product_part_material_option.values.Fabric A'
        ]);
        $manager->persist($roductPartMaterialVariant);
        
        $roductPartMaterialVariant = $this->createProductEtensionVariant('', '403 T', $roductPartMaterial, [
            'Furniture.product_part_material_option.values.Fabric A'
        ]);
        $manager->persist($roductPartMaterialVariant);
        
        $manager->flush();
    }
    
    /**
     * 
     * @param string $name
     * @param array $translations
     * @param array $extension_options
     * @return \Furniture\ProductBundle\Entity\ProductPartMaterial
     */
    protected function createProductPartMaterial($name, array $translations, array $extension_options){
        /* @var $roductPartMaterial \Furniture\ProductBundle\Entity\ProductPartMaterial */
        $roductPartMaterial = $this->get('furniture.repository.product_part_material')->createNew();
        $roductPartMaterial->setName($name);
        
        $this->setReference( 'Furniture.product_part_material.'.$name, $roductPartMaterial);
        
        foreach ($translations as $locale => $presentation) {
            $roductPartMaterial->setCurrentLocale($locale);
            $roductPartMaterial->setPresentation($presentation['presentation']);
        }
        
        foreach($extension_options as $extension_option){
            $extension_option_type = $extension_option['entity'];
            foreach($extension_option['values'] as $value){
                $extension_option_value = new ProductPartMaterialOptionValue();
                $extension_option_value->setValue($value);
                $extension_option_value->setOption($extension_option_type);
                $roductPartMaterial->addOptionValue($extension_option_value);
                $this->setReference('Furniture.product_part_material_option.values.'.$value, $extension_option_value);
            }
        }
        return $roductPartMaterial;
    }
    
    /**
     * 
     * @param string $itemName
     * @param string $itemCode
     * @param array $roductPartMaterialValues
     * @return \Furniture\ProductBundle\Entity\ProductPartMaterialVariant
     */
    protected function createProductEtensionVariant($itemName, $itemCode, $extension, array $roductPartMaterialValues){
        /* @var $roductPartMaterialVariant \Furniture\ProductBundle\Entity\ProductPartMaterialVariant */
        $roductPartMaterialVariant = $this->get('furniture.repository.product_part_material_variant')->createNew();
        $roductPartMaterialVariant->setDescriptionalName($itemName);
        $roductPartMaterialVariant->setDescriptionalCode($itemCode);
        $roductPartMaterialVariant->setMaterial($extension);
        
        foreach($roductPartMaterialValues as $valueReference){
            $roductPartMaterialVariant->addValue( $this->getReference($valueReference) );
        }
        
        return $roductPartMaterialVariant;
    }
    
    
}

