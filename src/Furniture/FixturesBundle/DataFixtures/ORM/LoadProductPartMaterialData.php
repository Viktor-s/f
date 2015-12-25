<?php

namespace Furniture\FixturesBundle\DataFixtures\ORM;

use Sylius\Bundle\FixturesBundle\DataFixtures\DataFixture;
use \Doctrine\Common\Persistence\ObjectManager;
use \Furniture\ProductBundle\Entity\ProductPartMaterialOptionValue;
use \Furniture\ProductBundle\Entity\ProductPartMaterialVariantImage;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Filesystem\Filesystem;


class LoadProductPartMaterialData extends DataFixture {
    
    /**
     * Product part materials images path
     * @var str
     */
    protected $imagePath = '/../../Resources/fixtures/productPartMaterial';

    public function getOrder() {
        return 20;
    }
    
    public function load(ObjectManager $manager) {
        
        /* Create Antonella Italia wood */
        $roductPartMaterial = $this->createProductPartMaterial(
                'antonello_italia_wood',
                [
                    $this->defaultLocale => [
                        'presentation' => 'antonello_italia_wood'
                    ]
                ],
                [
                    [
                        'entity' => $this->getReference('Furniture.product_part_material_option.category'),
                        'values' => [
                            'WOOD',
                            'SHINING LACQUERED',
                            'MATT LACQUERED',
                            'Fabric B',
                            'Fabric A',
                        ]
                    ],
                    [
                        'entity' => $this->getReference('Furniture.product_part_material_option.Color'),
                        'values' => [
                            'Dark Grey',
                            'Black',
                            'Red',
                            'White',
                            'Green',
                            'Cream',
                            'Dove Grey',
                            'Mud',
                            'Dark Brown',
                            'Light Grey',
                            'Ash Grey',
                            '-',
                        ]
                    ],
                ]
                );
        $manager->persist($roductPartMaterial);
        
        $roductPartMaterialVariant = $this->createProductEtensionVariant(
            'Smoked Oak WOOD', 
            '', 
            $roductPartMaterial, [
                'Furniture.product_part_material_option.values.WOOD'
            ],
            $this->getMaterialVariantImage('Smoked Oak WOOD.jpeg')
                );
        $manager->persist($roductPartMaterialVariant);
        
        $roductPartMaterialVariant = $this->createProductEtensionVariant(
            'Grey Oak WOOD', 
            '', 
            $roductPartMaterial, [
                'Furniture.product_part_material_option.values.WOOD'
            ],
            $this->getMaterialVariantImage('Grey Oak WOOD.jpeg')
                );
        $manager->persist($roductPartMaterialVariant);
        
        $roductPartMaterialVariant = $this->createProductEtensionVariant(
            'Wengè WOOD', 
            '', 
            $roductPartMaterial, [
                'Furniture.product_part_material_option.values.WOOD'
            ],
            $this->getMaterialVariantImage('Wengè WOOD.jpeg')
                );
        $manager->persist($roductPartMaterialVariant);
        
        $roductPartMaterialVariant = $this->createProductEtensionVariant(
            'Canaletto Walnut WOOD', 
            '', 
            $roductPartMaterial, [
                'Furniture.product_part_material_option.values.WOOD'
            ],
                $this->getMaterialVariantImage('Canaletto Walnut WOOD.jpeg')
                );
        $manager->persist($roductPartMaterialVariant);
        
        $roductPartMaterialVariant = $this->createProductEtensionVariant(
            'White-4071 White, MATT LACQUERED', 
            '', 
            $roductPartMaterial, [
                'Furniture.product_part_material_option.values.MATT LACQUERED',
                'Furniture.product_part_material_option.values.White',
            ],
                $this->getMaterialVariantImage('White-4071 White, MATT LACQUERED.jpeg')
                );
        $manager->persist($roductPartMaterialVariant);
        
        $roductPartMaterialVariant = $this->createProductEtensionVariant(
            'Ash Grey-1033 Ash Grey, SHINING LACQUERED', 
            '', 
            $roductPartMaterial, [
                'Furniture.product_part_material_option.values.SHINING LACQUERED',
                'Furniture.product_part_material_option.values.Ash Grey',
            ],
                $this->getMaterialVariantImage('Ash Grey-1033 Ash Grey, SHINING LACQUERED.jpeg')
                );
        $manager->persist($roductPartMaterialVariant);
        
        $roductPartMaterialVariant = $this->createProductEtensionVariant(
            'Light Grey-1030 Light Grey, SHINING LACQUERED', 
            '', 
            $roductPartMaterial, [
                'Furniture.product_part_material_option.values.SHINING LACQUERED',
                'Furniture.product_part_material_option.values.Light Grey',
            ],
                $this->getMaterialVariantImage('Light Grey-1030 Light Grey, SHINING LACQUERED.jpeg')
                );
        $manager->persist($roductPartMaterialVariant);
        
        $roductPartMaterialVariant = $this->createProductEtensionVariant(
            'Cream-1092 Cream, SHINING LACQUERED', 
            '', 
            $roductPartMaterial, [
                'Furniture.product_part_material_option.values.SHINING LACQUERED',
                'Furniture.product_part_material_option.values.Cream',
            ],
                $this->getMaterialVariantImage('Cream-1092 Cream, SHINING LACQUERED.jpeg')
                );
        $manager->persist($roductPartMaterialVariant);
        
        $roductPartMaterialVariant = $this->createProductEtensionVariant(
            'Light Grey-4030 Light Grey, MATT LACQUERED', 
            '', 
            $roductPartMaterial, [
                'Furniture.product_part_material_option.values.MATT LACQUERED',
                'Furniture.product_part_material_option.values.Light Grey',
            ],
                $this->getMaterialVariantImage('Light Grey-4030 Light Grey, MATT LACQUERED.jpeg')
                );
        $manager->persist($roductPartMaterialVariant);
        
        $roductPartMaterialVariant = $this->createProductEtensionVariant(
            'Red-4089 Ash Grey, MATT LACQUERED', 
            '', 
            $roductPartMaterial, [
                'Furniture.product_part_material_option.values.MATT LACQUERED',
                'Furniture.product_part_material_option.values.Ash Grey',
            ],
                $this->getMaterialVariantImage('Red-4089 Ash Grey, MATT LACQUERED.jpeg')
                );
        $manager->persist($roductPartMaterialVariant);
        
        $roductPartMaterialVariant = $this->createProductEtensionVariant(
            'Dove Grey-4079 Dove Grey, MATT LACQUERED', 
            '', 
            $roductPartMaterial, [
                'Furniture.product_part_material_option.values.MATT LACQUERED',
                'Furniture.product_part_material_option.values.Dove Grey',
            ],
                $this->getMaterialVariantImage('Dove Grey-4079 Dove Grey, MATT LACQUERED.jpeg')
                );
        $manager->persist($roductPartMaterialVariant);
        
        $roductPartMaterialVariant = $this->createProductEtensionVariant(
            'Ash Grey-4033 Ash Grey, MATT LACQUERED', 
            '', 
            $roductPartMaterial, [
                'Furniture.product_part_material_option.values.MATT LACQUERED',
                'Furniture.product_part_material_option.values.Ash Grey',
            ],
                $this->getMaterialVariantImage('Ash Grey-4033 Ash Grey, MATT LACQUERED.jpeg')
                );
        $manager->persist($roductPartMaterialVariant);
        
        $roductPartMaterialVariant = $this->createProductEtensionVariant(
            'Black-4073 Black, MATT LACQUERED', 
            '', 
            $roductPartMaterial, [
                'Furniture.product_part_material_option.values.MATT LACQUERED',
                'Furniture.product_part_material_option.values.Black',
            ],
                $this->getMaterialVariantImage('Black-4073 Black, MATT LACQUERED.jpeg')
                );
        $manager->persist($roductPartMaterialVariant);
        
        $roductPartMaterialVariant = $this->createProductEtensionVariant(
            'Mud-4006 Mud, MATT LACQUERED', 
            '', 
            $roductPartMaterial, [
                'Furniture.product_part_material_option.values.MATT LACQUERED',
                'Furniture.product_part_material_option.values.Mud',
            ],
                $this->getMaterialVariantImage('Mud-4006 Mud, MATT LACQUERED.jpeg')
                );
        $manager->persist($roductPartMaterialVariant);
        
        $roductPartMaterialVariant = $this->createProductEtensionVariant(
            'Mud-1006 Mud, SHINING LACQUERED', 
            '', 
            $roductPartMaterial, [
                'Furniture.product_part_material_option.values.SHINING LACQUERED',
                'Furniture.product_part_material_option.values.Mud',
            ],
                $this->getMaterialVariantImage('Mud-1006 Mud, SHINING LACQUERED.jpeg')
                );
        $manager->persist($roductPartMaterialVariant);
        
        /* Create Antonella Italia fabric */
        $roductPartMaterial = $this->createProductPartMaterial(
                'antonello_italia_fabric',
                [
                    $this->defaultLocale => [
                        'presentation' => 'antonello_italia_fabric'
                    ]
                ],
                [
                    [
                        'entity' => $this->getReference('Furniture.product_part_material_option.category'),
                        'values' => [
                            'Fabric A',
                            'Fabric B',
                        ]
                    ],
                ]
                );
        $manager->persist($roductPartMaterial);
        
        $roductPartMaterialVariant = $this->createProductEtensionVariant(
            '604 T', 
            '604 T', 
            $roductPartMaterial, [
                'Furniture.product_part_material_option.values.Fabric A',
            ],
                $this->getMaterialVariantImage('604 T.jpeg')
                );
        $manager->persist($roductPartMaterialVariant);
        
        $roductPartMaterialVariant = $this->createProductEtensionVariant(
            '802 T', 
            '802 T', 
            $roductPartMaterial, [
                'Furniture.product_part_material_option.values.Fabric A',
            ],
                $this->getMaterialVariantImage('802 T.jpeg')
                );
        $manager->persist($roductPartMaterialVariant);
        
        $roductPartMaterialVariant = $this->createProductEtensionVariant(
            '501 T', 
            '501 T', 
            $roductPartMaterial, [
                'Furniture.product_part_material_option.values.Fabric A',
            ],
                $this->getMaterialVariantImage('501 T.jpeg')
                );
        $manager->persist($roductPartMaterialVariant);
        
        $roductPartMaterialVariant = $this->createProductEtensionVariant(
            '603 T', 
            '603 T', 
            $roductPartMaterial, [
                'Furniture.product_part_material_option.values.Fabric A',
            ],
                $this->getMaterialVariantImage('603 T.jpeg')
                );
        $manager->persist($roductPartMaterialVariant);
        
        $roductPartMaterialVariant = $this->createProductEtensionVariant(
            '504 T', 
            '504 T', 
            $roductPartMaterial, [
                'Furniture.product_part_material_option.values.Fabric A',
            ],
                $this->getMaterialVariantImage('504 T.jpeg')
                );
        $manager->persist($roductPartMaterialVariant);
        
        $roductPartMaterialVariant = $this->createProductEtensionVariant(
            '806 T', 
            '806 T', 
            $roductPartMaterial, [
                'Furniture.product_part_material_option.values.Fabric A',
            ],
                $this->getMaterialVariantImage('806 T.jpeg')
                );
        $manager->persist($roductPartMaterialVariant);
        
        $roductPartMaterialVariant = $this->createProductEtensionVariant(
            '601 T', 
            '601 T', 
            $roductPartMaterial, [
                'Furniture.product_part_material_option.values.Fabric A',
            ],
                $this->getMaterialVariantImage('601 T.jpeg')
                );
        $manager->persist($roductPartMaterialVariant);
        
        $roductPartMaterialVariant = $this->createProductEtensionVariant(
            '502 T', 
            '502 T', 
            $roductPartMaterial, [
                'Furniture.product_part_material_option.values.Fabric A',
            ],
                $this->getMaterialVariantImage('502 T.jpeg')
                );
        $manager->persist($roductPartMaterialVariant);
        
        $roductPartMaterialVariant = $this->createProductEtensionVariant(
            '903 T', 
            '903 T', 
            $roductPartMaterial, [
                'Furniture.product_part_material_option.values.Fabric A',
            ],
                $this->getMaterialVariantImage('903 T.jpeg')
                );
        $manager->persist($roductPartMaterialVariant);
        
        $roductPartMaterialVariant = $this->createProductEtensionVariant(
            '904 T', 
            '904 T', 
            $roductPartMaterial, [
                'Furniture.product_part_material_option.values.Fabric A',
            ],
                $this->getMaterialVariantImage('904 T.jpeg')
                );
        $manager->persist($roductPartMaterialVariant);
        
        $roductPartMaterialVariant = $this->createProductEtensionVariant(
            '905 T', 
            '905 T', 
            $roductPartMaterial, [
                'Furniture.product_part_material_option.values.Fabric A',
            ],
                $this->getMaterialVariantImage('905 T.jpeg')
                );
        $manager->persist($roductPartMaterialVariant);
        
        $roductPartMaterialVariant = $this->createProductEtensionVariant(
            '602 T', 
            '602 T', 
            $roductPartMaterial, [
                'Furniture.product_part_material_option.values.Fabric A',
            ],
                $this->getMaterialVariantImage('602 T.jpeg')
                );
        $manager->persist($roductPartMaterialVariant);
        
        $roductPartMaterialVariant = $this->createProductEtensionVariant(
            '520 T', 
            '520 T', 
            $roductPartMaterial, [
                'Furniture.product_part_material_option.values.Fabric B',
            ],
                $this->getMaterialVariantImage('520 T.jpeg')
                );
        $manager->persist($roductPartMaterialVariant);
        
        $roductPartMaterialVariant = $this->createProductEtensionVariant(
            '521 T', 
            '521 T', 
            $roductPartMaterial, [
                'Furniture.product_part_material_option.values.Fabric B',
            ],
                $this->getMaterialVariantImage('521 T.jpeg')
                );
        $manager->persist($roductPartMaterialVariant);
        
        $roductPartMaterialVariant = $this->createProductEtensionVariant(
            '522 T', 
            '522 T', 
            $roductPartMaterial, [
                'Furniture.product_part_material_option.values.Fabric B',
            ],
                $this->getMaterialVariantImage('522 T.jpeg')
                );
        $manager->persist($roductPartMaterialVariant);
        
        $roductPartMaterialVariant = $this->createProductEtensionVariant(
            '921 T', 
            '921 T', 
            $roductPartMaterial, [
                'Furniture.product_part_material_option.values.Fabric B',
            ],
                $this->getMaterialVariantImage('921 T.jpeg')
                );
        $manager->persist($roductPartMaterialVariant);
        
        $roductPartMaterialVariant = $this->createProductEtensionVariant(
            '821 T', 
            '821 T', 
            $roductPartMaterial, [
                'Furniture.product_part_material_option.values.Fabric B',
            ],
                $this->getMaterialVariantImage('821 T.jpeg')
                );
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
    
    protected function getMaterialVariantImage($name)
    {
        $uploader = $this->get('sylius.image_uploader');
        $flPath = __DIR__.$this->imagePath.'/'.$name;
        /* @var $image \Furniture\ProductBundle\Entity\ProductPartMaterialVariantImage */
        $image = $this->get('furniture.repository.product_part_material_variant_image')->createNew();
        $imgFile = new \SplFileInfo($flPath);
        $image->setFile(new UploadedFile($imgFile->getRealPath(), $imgFile->getFilename()));
        $uploader->upload($image);
        return $image;
    }


    /**
     * 
     * @param string $itemName
     * @param string $itemCode
     * @param array $roductPartMaterialValues
     * @return \Furniture\ProductBundle\Entity\ProductPartMaterialVariant
     */
    protected function createProductEtensionVariant($itemName, $itemCode, $extension, array $roductPartMaterialValues, ProductPartMaterialVariantImage $image){
        /* @var $roductPartMaterialVariant \Furniture\ProductBundle\Entity\ProductPartMaterialVariant */
        $roductPartMaterialVariant = $this->get('furniture.repository.product_part_material_variant')->createNew();
        $roductPartMaterialVariant->setDescriptionalName($itemName);
        $roductPartMaterialVariant->setDescriptionalCode($itemCode);
        $roductPartMaterialVariant->setMaterial($extension);
        $roductPartMaterialVariant->setImage($image);
        
        foreach($roductPartMaterialValues as $valueReference){
            $roductPartMaterialVariant->addValue( $this->getReference($valueReference) );
        }
        
        return $roductPartMaterialVariant;
    }
    
    
}

