<?php

namespace Furniture\ProductBundle\Generator;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Furniture\ProductBundle\Entity\PdpIntellectualCompositeExpression;
use Furniture\ProductBundle\Entity\PdpIntellectualElement;
use Furniture\ProductBundle\Entity\PdpIntellectualRoot;
use Furniture\ProductBundle\Entity\ProductScheme;


class PdpIntelligentSchemesGenerator
{
    /**
     * @var Collection|ProductScheme[]
     */
    private $schemes;

    /**
     * @var Collection
     */
    private $levels;

    /**
     * @var Collection
     */
    private $schemesLevels;

    /**
     * @var PdpIntellectualRoot
     */
    private $pdpRoot;

    /**
     * PdpIntelligentSchemesGenerator constructor.
     * @param PdpIntellectualRoot $root
     */
    public function __construct(PdpIntellectualRoot $root)
    {
        $this->pdpRoot = $root;
        $this->levels = new ArrayCollection();
        $this->schemes = new ArrayCollection();
        $this->schemesLevels = new ArrayCollection();
    }

    /**
     *
     */
    public function generate()
    {
        $this->getLevels();

        for ($i = 0; $i < $this->levels->count(); $i++) {
            if ($i === 0) {
                continue;
            }
            $level = $i - 1;
            /** @var Collection|PdpIntellectualCompositeExpression[] $expressions */
            $expressions = $this->levels->get($i);

            if ($level === 0) {
                $this->schemesLevels->clear();
                $schemeData = new ArrayCollection();
                $schemeData->add($expressions);
                $this->schemesLevels->add($schemeData);
            } else {
                $type = $expressions->first()->getType();

                if ($type === PdpIntellectualCompositeExpression::TYPE_AND) { // Answers
                    $processed = new ArrayCollection();
                    while (!$expressions->isEmpty()) {
                        $first = $expressions->first();
                        $expressions->removeElement($first);

                        /** @var Collection|PdpIntellectualCompositeExpression[] $ancestorQuestions */
                        // Get all neighbors questions for answer.
                        $ancestorQuestions = $first->getParent()->getParent()->getChild();

                        // Get partitions of raw schemes that contains all answers on current step.
                        $partitions = $this->schemesLevels->partition(function ($key, $collection) use ($ancestorQuestions, $level) {
                            if ($ancestorQuestions->isEmpty()) {
                                return false;
                            }

                            /** @var Collection|PdpIntellectualCompositeExpression[] $collection */
                            return $ancestorQuestions->forAll(function ($key, $expression) use ($collection, $level) {
                                return $collection->containsKey($level - 1) && $collection->get($level - 1)->contains($expression);
                            });
                        });

                        // Not matched raw schemes leave without changes.
                        $this->schemesLevels = $partitions[1];

                        // Set answers as processed to exclude from secondary usage.
                        foreach ($ancestorQuestions as $expression) {
                            /** @var Collection $children */
                            $children = $expression->getChild();
                            $processed = new ArrayCollection(array_merge($processed->toArray(), $children->toArray()));
                        }

                        $expressions = $expressions->filter(function ($expression) use ($processed) {
                            return !$processed->contains($expression);
                        });

                        // Get all possible combinations for questions on current step.
                        $combinations = $this->getCombinations($ancestorQuestions);
                        // Add each combinations to all matched raw schemes to current step.
                        /** @var Collection $collection */
                        foreach ($partitions[0] as $collection) {
                            /** @var Collection $combination */
                            foreach ($combinations as $combination) {
                                $newSchemeData = new ArrayCollection($collection->toArray());
                                $data = ($combination instanceof Collection) ? $combination : new ArrayCollection($combination);
                                $newSchemeData->set($level, $data);

                                $this->schemesLevels->add($newSchemeData);
                            }
                        }
                    }
                } else if ($type === PdpIntellectualCompositeExpression::TYPE_OR) { // Questions
                    $processed = new ArrayCollection();
                    while (!$expressions->isEmpty()) {
                        $first = $expressions->first();
                        $expressions->removeElement($first);

                        // Get neighbors questions.
                        /** @var PdpIntellectualCompositeExpression $parent */
                        $parent = $first->getParent();
                        /** @var Collection|PdpIntellectualCompositeExpression[] $neighborQuestions */
                        $neighborQuestions = $parent->getChild();
                        $parentCollection = new ArrayCollection();
                        $parentCollection->add($parent);

                        // Get partitions of raw schemes that contains all answers on current step.
                        $partitions = $this->schemesLevels->partition(function ($key, $collection) use ($parentCollection, $level){
                            /** @var Collection|PdpIntellectualCompositeExpression[] $collection */
                            return $parentCollection->forAll(function ($key, $expression) use ($collection, $level) {
                                return $collection->containsKey($level - 1) && $collection->get($level - 1)->contains($expression);
                            });
                        });

                        // Not matched raw schemes leave without changes.
                        $this->schemesLevels = $partitions[1];
                        // Set answers as processed to exclude from secondary usage.
                        foreach ($neighborQuestions as $expression) {
                            /** @var Collection $children */
                            $children = $expression->getChild();
                            $processed = new ArrayCollection(array_merge($processed->toArray(), $children->toArray()));
                        }

                        $expressions = $expressions->filter(function ($expression) use ($processed) {
                            return !$processed->contains($expression);
                        });

                        // Add questions to all matched raw schemes to current step.
                        /** @var Collection $collection */
                        foreach ($partitions[0] as $collection) {
                            /** @var Collection $combination */
                            if (!$collection->containsKey($level)) {
                                $collection->set($level, new ArrayCollection());
                            }
                            $temp = $neighborQuestions->toArray();
                            $temp2 = $collection->get($level)->toArray();
                            $collection->set($level, new ArrayCollection(array_merge($temp, $temp2)));

                            $this->schemesLevels->add($collection);
                        }
                    }
                }
            }
        }
    }


    /**
     * Get levels of root element.
     */
    private function getLevels()
    {
        static $level = 0;

        if ($level === 0) {
            $this->levels->clear();
            $this->levels->set($level, new ArrayCollection([$this->pdpRoot->getExpression()]));
        }

        $successors = [];

        /** @var PdpIntellectualCompositeExpression $expression */
        foreach ($this->levels->get($level) as $expression) {
            $successors = array_merge($successors, $expression->getChild()->toArray());
        }

        if (!empty($successors)) {
            $this->levels->set(++$level, new ArrayCollection($successors));
            $this->getLevels();
        }
    }


    /**
     * @param Collection|PdpIntellectualCompositeExpression[] $expressions
     * @return ArrayCollection
     */
    private function getCombinations($expressions)
    {
        /** @var  PdpIntellectualCompositeExpression $first */
        $first = $expressions->first();
        $expressions->removeElement($first);
        $result = $first->getChild();

        while (!$expressions->isEmpty()) {
            $first = $expressions->first();
            $expressions->removeElement($first);
            $newCombination = new ArrayCollection();
            foreach ($result as $expression) {
                $children = $first->getChild();
                foreach ($children as $child) {
                    if ($expression instanceof Collection) {
                        $expression->add($child);
                        $newCombination->add($expression);
                    } else {
                        $newCombination->add(new ArrayCollection([$expression, $child]));
                    }
                }
            }
            $result = $newCombination;
        }

        return $result;
    }

    /**
     * Has product schemes
     *
     * @return bool
     */
    public function hasProductSchemes()
    {
        return !(bool)$this->schemes->isEmpty();
    }

    /**
     * Has product scheme
     *
     * @param ProductScheme $productScheme
     *
     * @return bool
     */
    public function hasProductScheme(ProductScheme $productScheme)
    {
        return $this->schemes->contains($productScheme);
    }

    /**
     * Grt product schemes
     *
     * @return Collection|ProductScheme[]
     */
    public function getProductSchemes()
    {
        return $this->schemes;
    }

    /**
     * Set product part schemes
     *
     * @param Collection $productSchemes
     *
     * @return PdpIntelligentSchemesGenerator
     */
    public function setProductSchemes(Collection $productSchemes)
    {
        $this->schemes = $productSchemes;

        return $this;
    }

    /**
     * Add product scheme
     *
     * @param ProductScheme $productScheme
     *
     * @return PdpIntelligentSchemesGenerator
     */
    public function addProductScheme(ProductScheme $productScheme)
    {
        if (!$this->hasProductScheme($productScheme)) {
            $productScheme->setProduct($this->pdpRoot->getProduct());
            $this->schemes->add($productScheme);
        }

        return $this;
    }

    /**
     * Remove product scheme
     *
     * @param ProductScheme $productScheme
     *
     * @return PdpIntelligentSchemesGenerator
     */
    public function removeProductScheme(ProductScheme $productScheme)
    {
        if ($this->hasProductScheme($productScheme)) {
            $this->schemes->removeElement($productScheme);
        }

        return $this;
    }

}