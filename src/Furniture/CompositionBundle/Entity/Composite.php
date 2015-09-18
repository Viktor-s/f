<?php

namespace Furniture\CompositionBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Translation\Model\AbstractTranslatable;

class Composite extends AbstractTranslatable
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var CompositeTemplate
     */
    protected $template;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var Collection|CompositeProduct[]
     */
    protected $products;

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
     * @return Composite
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
     * Set template
     *
     * @param CompositeTemplate $template
     *
     * @return Composite
     */
    public function setTemplate(CompositeTemplate $template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Get template
     *
     * @return CompositeTemplate
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Set products
     *
     * @param Collection|CompositeProduct[] $products
     *
     * @return Composite
     */
    public function setProducts(Collection $products)
    {
        $this->products = $products;

        return $this;
    }

    /**
     * Get products
     *
     * @return Collection|CompositeProduct[]
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * Get products grouped by category
     *
     * @return array
     */
    public function getProductsGroupedByCategory()
    {
        $categories = [];

        foreach ($this->products as $compositeProduct) {
            $taxon = $compositeProduct->getTemplateItem()->getTaxon();

            if (!isset($categories[$taxon->getId()])) {
                $categories[$taxon->getId()] = [
                    'category' => $taxon,
                    'products' => [],
                    'product_names' => []
                ];
            }

            $product = $compositeProduct->getProduct();

            $categories[$taxon->getId()]['products'][] = $product;
            $categories[$taxon->getId()]['product_names'][] = $product->getName();
        }

        return $categories;
    }
}
