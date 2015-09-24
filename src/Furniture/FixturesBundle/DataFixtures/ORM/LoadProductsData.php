<?php

namespace Furniture\FixturesBundle\DataFixtures\ORM;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\FixturesBundle\DataFixtures\DataFixture;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Product\Model\AttributeValueInterface;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;


use Sylius\Bundle\FixturesBundle\DataFixtures\ORM\LoadProductsData as BaseLoadProductsData;

class LoadProductsData extends BaseLoadProductsData
{
    /**
     * Total variants created.
     *
     * @var integer
     */
    private $totalVariants = 0;

    private  $channels = array(
        'WEB-UK',
        'WEB-DE',
        'WEB-US',
        'MOBILE',
    );

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i <= 120; $i++) {
            switch (rand(0, 3)) {
                case 0:
                    $manager->persist($this->createTable($i));
                    break;

                case 1:
                    //$manager->persist($this->createFurnture($i));
                    break;

                case 2:
                    //$manager->persist($this->createFurnture($i));
                    break;

                case 3:
                    //$manager->persist($this->createFurnture($i));
                    break;
            }

            if (0 === $i % 20) {
                $manager->flush();
            }
        }

        $manager->flush();

        $this->defineTotalVariants();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 50;
    }

    /**
     * Creates t-shirt product.
     *
     * @param integer $i
     *
     * @return ProductInterface
     */
    protected function createTable($i)
    {
        $product = $this->createProduct();
        //$product->setTaxCategory($this->getTaxCategory('Taxable goods'));

        $translatedNames = array(
            $this->defaultLocale => sprintf('Table "%s"', $this->faker->word),
            'es_ES' => sprintf('Camiseta "%s"', $this->fakers['es_ES']->word),
        );
        $this->addTranslatedFields($product, $translatedNames);

        $product->setVariantSelectionMethod(ProductInterface::VARIANT_SELECTION_MATCH);

        $this->addMasterVariant($product);
        $this->setChannels($product, $this->faker->randomElements($this->channels, rand(1, 4)));

        $this->setTaxons($product, array('Table'));

        $randomDesigner = $this->faker->randomElement(
                array(
                    'Eero Aarnio',
                    'Thomas Affleck', 
                    'Joseph Brennan', 
                    'John Cobb',
                    'Antonio Citterio',
                    'Paul Evans',
                    'T. H. Robsjohn-Gibbings')
                );
        $this->addAttribute($product, 'Designer', $randomDesigner);

        $product->addOption($this->getReference('Sylius.Option.table_legs'));
        $product->addOption($this->getReference('Sylius.Option.table_tops'));
        
        $product->setFactory(
                $this->getReference( 
                        'Furniture.factory.'.$this->faker->randomElement(
                                [
                                    'Selva',
                                    'Antonello Italia',
                                    'Kartell',
                                    'Arceos',
                                    'Passini',
                                    'i4 Mariani'
                                ]
                                ) )
                );
        
        $this->generateVariants($product);

        /* generate sku options */
        $num = rand(0,4);
        if($num){
            $sizes = array_slice([
                '1x5',
                '1x10',
                '2x3',
                '4x6',
            ], 0, $num);
            
            foreach (  $sizes as $size ){
                $sku_option = new \Furniture\SkuOptionBundle\Entity\SkuOptionVariant();
                $sku_option->setSkuOptionType($this->getReference('Furniture.sku_option.Size'));
                $sku_option->setValue($size);
                $product->addSkuOptionVariant($sku_option);
            }
        }
        
        $this->setReference('Sylius.Product.'.$i, $product);

        return $product;
    }

    /**
     * Generates all possible variants with random prices.
     *
     * @param ProductInterface $product
     */
    protected function generateVariants(ProductInterface $product)
    {
        $this
            ->getVariantGenerator()
            ->generate($product)
        ;

        foreach ($product->getVariants() as $variant) {
            $variant->setAvailableOn($this->faker->dateTimeThisYear);
            $variant->setPrice($this->faker->randomNumber(4));
            $variant->setSku($this->getUniqueSku());
            $variant->setOnHand($this->faker->randomNumber(1));

            $this->setReference('Sylius.Variant-'.$this->totalVariants, $variant);

            ++$this->totalVariants;
        }
    }

    /**
     * Adds master variant to product.
     *
     * @param ProductInterface $product
     * @param string           $sku
     */
    protected function addMasterVariant(ProductInterface $product, $sku = null)
    {
        $variant = $product->getMasterVariant();
        $variant->setProduct($product);
        $variant->setPrice($this->faker->randomNumber(4));
        $variant->setSku(null === $sku ? $this->getUniqueSku() : $sku);
        $variant->setAvailableOn($this->faker->dateTimeThisYear);
        $variant->setOnHand($this->faker->randomNumber(1));

        $productName = explode(' ', $product->getName());
        $image = clone $this->getReference(
            'Sylius.Image.Product.'.$productName[0].'-'.rand(1,8)
        );
        $variant->addImage($image);

        $this->setReference('Sylius.Variant-'.$this->totalVariants, $variant);

        ++$this->totalVariants;

        $product->setMasterVariant($variant);
    }

    /**
     * Adds attribute to product with given value.
     *
     * @param ProductInterface $product
     * @param string           $name
     * @param string           $value
     */
    private function addAttribute(ProductInterface $product, $name, $value)
    {
        /* @var $attribute AttributeValueInterface */
        $attribute = $this->getProductAttributeValueRepository()->createNew();
        $attribute->setAttribute($this->getReference('Sylius.Attribute.'.$name));
        $attribute->setProduct($product);
        $attribute->setValue($value);

        $product->addAttribute($attribute);
    }

    /**
     * Adds taxons to given product.
     *
     * @param ProductInterface $product
     * @param array            $taxonNames
     */
    protected function setTaxons(ProductInterface $product, array $taxonNames)
    {
        $taxons = new ArrayCollection();

        foreach ($taxonNames as $taxonName) {
            $taxons->add($this->getReference('Sylius.Taxon.'.$taxonName));
        }

        $product->setTaxons($taxons);
    }

    /**
     * Set channels.
     *
     * @param ProductInterface $product
     * @param array            $channelCodes
     */
    protected function setChannels(ProductInterface $product, array $channelCodes)
    {
        foreach ($channelCodes as $code) {
            $product->addChannel($this->getReference('Sylius.Channel.'.$code));
        }
    }

    /**
     * Get tax category by name.
     *
     * @param string $name
     *
     * @return TaxCategoryInterface
     */
    protected function getTaxCategory($name)
    {
        return $this->getReference('Sylius.TaxCategory.'.ucfirst($name));
    }

    /**
     * Get unique SKU.
     *
     * @param integer $length
     *
     * @return string
     */
    protected function getUniqueSku($length = 5)
    {
        return $this->faker->unique()->randomNumber($length);
    }

    /**
     * Get unique ISBN number.
     *
     * @return string
     */
    protected function getUniqueISBN()
    {
        return $this->faker->unique()->uuid();
    }

    /**
     * Create new product instance.
     *
     * @return ProductInterface
     */
    protected function createProduct()
    {
        return $this->getProductRepository()->createNew();
    }

    /**
     * Define constant with number of total variants created.
     */
    protected function defineTotalVariants()
    {
        define('SYLIUS_FIXTURES_TOTAL_VARIANTS', $this->totalVariants);
    }

    private function addTranslatedFields(ProductInterface $product, $translatedNames)
    {
        foreach ($translatedNames as $locale => $name) {
            $product->setCurrentLocale($locale);
            $product->setFallbackLocale($locale);

            $product->setName($name);
            $product->setDescription($this->fakers[$locale]->paragraph);
            $product->setShortDescription($this->fakers[$locale]->sentence);
            $product->setMetaKeywords(str_replace(' ', ', ', $this->fakers[$locale]->sentence));
            $product->setMetaDescription($this->fakers[$locale]->sentence);
        }

        $product->setCurrentLocale($this->defaultLocale);
    }
}
