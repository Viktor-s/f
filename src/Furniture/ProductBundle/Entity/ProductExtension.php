<?php

namespace Furniture\ProductBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FiveLab\Component\Exception\UnexpectedTypeException;
use Furniture\ProductBundle\Model\ProductExtensionOptionValueGrouped;
use Sylius\Component\Translation\Model\AbstractTranslatable;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Translation\Model\TranslationInterface;

/**
 * Product extension
 */
class ProductExtension extends AbstractTranslatable
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
     * @var Collection|ProductExtensionOptionValue[]
     */
    protected $optionValues;

    /**
     * @var Collection|ProductExtensionVariant[]
     */
    protected $variants;

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
        if (!$translation instanceof ProductExtensionTranslation) {
            throw UnexpectedTypeException::create($translation, ProductExtensionTranslation::class);
        }

        return parent::addTranslation($translation);
    }

    /**
     * {@inheritDoc}
     */
    public function removeTranslation(TranslationInterface $translation)
    {
        if (!$translation instanceof ProductExtensionTranslation) {
            throw UnexpectedTypeException::create($translation, ProductExtensionTranslation::class);
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
     * @return ProductExtension
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
     * @return ProductExtension
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
     * @return ProductExtension
     */
    public function setOptionValues(Collection $optionValues)
    {
        $optionValues->forAll(function ($key, $optionValue){
            if (!$optionValue instanceof ProductExtensionOptionValue) {
                throw UnexpectedTypeException::create($optionValue, ProductExtensionOptionValue::class);
            }

            $optionValue->setExtension($this);

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
     * @return ProductExtensionOptionValueGrouped|ProductExtensionOptionValueGrouped[]
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

            $result->add(new ProductExtensionOptionValueGrouped($option, $values));
        }

        return $result;
    }

    /**
     * Add option value
     *
     * @param ProductExtensionOptionValue $optionValue
     *
     * @return ProductExtension
     */
    public function addOptionValue(ProductExtensionOptionValue $optionValue)
    {
        if (!$this->optionValues->contains($optionValue)) {
            $optionValue->setExtension($this);
            $this->optionValues->add($optionValue);
        }

        return $this;
    }

    /**
     * Remove option
     *
     * @param ProductExtensionOptionValue $optionValue
     *
     * @return ProductExtension
     */
    public function removeOptionValue(ProductExtensionOptionValue $optionValue)
    {
        if ($this->optionValues->contains($optionValue)) {
            $optionValue->setExtension(null);
            $this->optionValues->removeElement($optionValue);
        }

        return $this;
    }

    /**
     * Get all variants for this extension
     *
     * @return Collection|ProductExtensionVariant[]
     */
    public function getVariants()
    {
        return $this->variants;
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
