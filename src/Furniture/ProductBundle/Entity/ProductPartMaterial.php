<?php

namespace Furniture\ProductBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FiveLab\Component\Exception\UnexpectedTypeException;
use Furniture\FactoryBundle\Entity\Factory;
use Furniture\ProductBundle\Model\ProductPartMaterialOptionValueGrouped;
use Sylius\Component\Translation\Model\AbstractTranslatable;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Translation\Model\TranslationInterface;

/**
 * Product part material
 */
class ProductPartMaterial extends AbstractTranslatable
{
    /**
     * @var int
     */
    protected $id;

    /**
     * Internal name
     *
     * @var string
     */
    protected $name;

    /**
     * Displaying for user
     *
     * @var string
     */
    protected $presentation;

    /**
     * @var Collection|ProductPartMaterialOptionValue[]
     */
    protected $optionValues;

    /**
     * @var Collection|ProductPartMaterialVariant[]
     */
    protected $variants;

    /**
     * @var Factory
     */
    private $factory;

    /**
     * Construct
     */
    public function __construct()
    {
        parent::__construct();

        $this->optionValues = new ArrayCollection();
        $this->variants = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function addTranslation(TranslationInterface $translation)
    {
        if (!$translation instanceof ProductPartMaterialTranslation) {
            throw UnexpectedTypeException::create($translation, ProductPartMaterialTranslation::class);
        }

        return parent::addTranslation($translation);
    }

    /**
     * {@inheritDoc}
     */
    public function removeTranslation(TranslationInterface $translation)
    {
        if (!$translation instanceof ProductPartMaterialTranslation) {
            throw UnexpectedTypeException::create($translation, ProductPartMaterialTranslation::class);
        }

        return parent::removeTranslation($translation);
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return ProductPartMaterial
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set presentation
     *
     * @param string $presentation
     *
     * @return ProductPartMaterial
     */
    public function setPresentation($presentation)
    {
        $this->translate()->setPresentation($presentation);

        return $this;
    }

    /**
     * Get presentation
     *
     * @return string
     */
    public function getPresentation()
    {
        return $this->translate()->getPresentation();
    }

    /**
     * Set option values
     *
     * @param Collection $optionValues
     *
     * @return ProductPartMaterial
     */
    public function setOptionValues(Collection $optionValues)
    {
        $optionValues->forAll(function ($key, $optionValue){
            if (!$optionValue instanceof ProductPartMaterialOptionValue) {
                throw UnexpectedTypeException::create($optionValue, ProductPartMaterialOptionValue::class);
            }

            $optionValue->setMaterial($this);

            return true;
        });

        $this->optionValues = $optionValues;

        return $this;
    }

    /**
     * Get option values
     *
     * @return Collection
     */
    public function getOptionValues()
    {
        return $this->optionValues;
    }

    /**
     * Get grouped option values
     *
     * @return ProductPartMaterialOptionValueGrouped|ProductPartMaterialOptionValueGrouped[]
     */
    public function getGroupedOptionValues()
    {
        $data = [];

        foreach ($this->optionValues as $optionValue) {
            $option = $optionValue->getOption();
            $optionName = $option->getName();

            if (!isset($data[$optionName])) {
                $data[$optionName] = [
                    'option' => $option,
                    'values' => []
                ];
            }

            $data[$optionName]['values'][] = $optionValue;
        }

        $result = new ArrayCollection();

        foreach ($data as $info) {
            $option = $info['option'];
            $values = new ArrayCollection($info['values']);

            $result->add(new ProductPartMaterialOptionValueGrouped($option, $values));
        }

        return $result;
    }

    /**
     * Add option value
     *
     * @param ProductPartMaterialOptionValue $optionValue
     *
     * @return ProductPartMaterial
     */
    public function addOptionValue(ProductPartMaterialOptionValue $optionValue)
    {
        if (!$this->optionValues->contains($optionValue)) {
            $optionValue->setMaterial($this);
            $this->optionValues->add($optionValue);
        }

        return $this;
    }

    /**
     * Remove option
     *
     * @param ProductPartMaterialOptionValue $optionValue
     *
     * @return ProductPartMaterial
     */
    public function removeOptionValue(ProductPartMaterialOptionValue $optionValue)
    {
        if ($this->optionValues->contains($optionValue)) {
            $optionValue->setMaterial(null);
            $this->optionValues->removeElement($optionValue);
        }

        return $this;
    }

    /**
     * Get all variants for this product part material
     *
     * @return Collection|ProductPartMaterialVariant[]
     */
    public function getVariants()
    {
        return $this->variants;
    }

    /**
     * Set factory
     *
     * @param Factory $factory
     *
     * @return ProductPartMaterial
     */
    public function setFactory(Factory $factory = null)
    {
        $this->factory = $factory;

        return $this;
    }

    /**
     * Get factory
     *
     * @return Factory
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * Implement __toString
     *
     * @return string
     */
    public function __toString()
    {
        return $this->name ?: '';
    }
}
