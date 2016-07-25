<?php

namespace Furniture\ProductBundle\Generator;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Furniture\ProductBundle\Entity\PdpIntellectualCompositeExpression;
use Furniture\ProductBundle\Entity\PdpIntellectualElement;
use Furniture\ProductBundle\Entity\PdpIntellectualRoot;
use Furniture\ProductBundle\Entity\ProductScheme;
use Symfony\Component\DependencyInjection\ContainerAware;


class PdpIntelligentSchemesGenerator extends ContainerAware
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
    private $schemesData;

    /**
     * @var Collection
     */
    private $pathToElements;
    
    /**
     * @var PdpIntellectualRoot
     */
    private $pdpRoot;

    /**
     * PdpIntelligentSchemesGenerator constructor.
     */
    public function __construct()
    {
        $this->levels = new ArrayCollection();
        $this->schemes = new ArrayCollection();
        $this->schemesData = new ArrayCollection();
        $this->pathToElements = new ArrayCollection();
    }

    /**
     * 
     * @param PdpIntellectualElement $element
     */
    private function addPathToElement(PdpIntellectualElement $element){
        
        $walk = function(PdpIntellectualCompositeExpression $e, &$path)use(&$walk){
            $path[] = $e;
            /* @var $e PdpIntellectualCompositeExpression */
            if($e->getParent()){
                $walk($e->getParent(), $path);
            }else{
                $path[] = $this->pdpRoot;
            }
        };
        
        $path = [$element];
        
        $walk($element->getExpression(), $path );
        $this->pathToElements->add(array_reverse($path));
    }
    
    public function getPathToElements(){
        return $this->pathToElements;
    }


    /**
     *
     */
    public function generate()
    {
        $this->getSchemesData();
        $localeProvider = $this->container->get('sylius.locale_provider');

        $this->schemesData->forAll(function($key, $collection) use ($localeProvider) {
            $scheme = new ProductScheme();
            $scheme->setProduct($this->pdpRoot->getProduct());
            $scheme->setCurrentLocale($localeProvider->getDefaultLocale());
            $scheme->setFallbackLocale($localeProvider->getDefaultLocale());
            /* @var $collection \Doctrine\Common\Collections\Collection  */
            $collection->forAll(function ($key, $expressions) use ($scheme) {
                /* @var $expressions \Doctrine\Common\Collections\Collection */
                $expressions->forAll(function ($key, $expression) use ($scheme) {
                    /* @var $expression PdpIntellectualCompositeExpression */
                    $expression->getElements()->forAll(function ($key, $element) use ($scheme, $expression) {
                        /* @var $element PdpIntellectualElement */
                        $input = $element->getInput();
                        if ($input->getProductPart()) {
                            if (!$scheme->hasProductPart($input->getProductPart())) {
                                $scheme->setName($scheme->getName().$input->getHumanName().' | ');
                                $scheme->addProductPart($input->getProductPart());
                                
                                $this->addPathToElement($element);
                                
                            }
                        }

                        return true;
                    });

                    return true;
                });

                return true;
            });
            $scheme->setName(substr($scheme->getName(), 0, -3));
            $this->schemes->add($scheme);

            return true;
        });
    }


    /**
     * Get levels of root element.
     */
    private function getLevels()
    {
        if ($this->pdpRoot) {
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
    }

    private function getSchemesData()
    {
        $this->getLevels();
        $this->schemesData->clear();
        for ($level = 0; $level < $this->levels->count(); $level++) {
            /** @var Collection|PdpIntellectualCompositeExpression[] $expressions */
            $expressions = $this->levels->get($level);

            if ($level === 0) {
                $schemeData = new ArrayCollection();
                $schemeData->add($expressions);
                $this->schemesData->set($level, $schemeData);
            } else if ($level === 1) {
                $this->schemesData->get($level - 1)->add($expressions);
            } else {
                $type = $expressions->first()->getType();

                if ($type === PdpIntellectualCompositeExpression::TYPE_AND) { // Answers
                    $processed = new ArrayCollection();
                    while (!$expressions->isEmpty()) {
                        $first = $expressions->first();
                        $expressions->removeElement($first);

                        /** @var Collection|PdpIntellectualCompositeExpression[] $ancestorQuestions */
                        // Get all neighbors questions for answer.
                        $ancestorQuestions = clone $first->getParent()->getParent()->getChild();

                        // Get partitions of raw schemes that contains all answers on current step.
                        $partitions = $this->schemesData->partition(function ($key, $collection) use ($ancestorQuestions, $level) {
                            if ($ancestorQuestions->isEmpty()) {
                                return false;
                            }

                            /** @var Collection|PdpIntellectualCompositeExpression[] $collection */
                            return $ancestorQuestions->forAll(function ($key, $expression) use ($collection, $level) {
                                return $collection->containsKey($level - 1) && $collection->get($level - 1)->contains($expression);
                            });
                        });

                        // Not matched raw schemes leave without changes.
                        $this->schemesData = $partitions[1];

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
                                $data = $combination;
                                $newSchemeData->set($level, $data);

                                $this->schemesData->add($newSchemeData);
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
                        $partitions = $this->schemesData->partition(function ($key, $collection) use ($parentCollection, $level){
                            /** @var Collection|PdpIntellectualCompositeExpression[] $collection */
                            return $parentCollection->forAll(function ($key, $expression) use ($collection, $level) {
                                return $collection->containsKey($level - 1) && $collection->get($level - 1)->contains($expression);
                            });
                        });

                        // Not matched raw schemes leave without changes.
                        $this->schemesData = $partitions[1];
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

                            $this->schemesData->add($collection);
                        }
                    }
                }
            }
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

        if ($expressions->isEmpty()) {
            $tmp = $result;
            $result = new ArrayCollection();
            foreach ($tmp as $expression) {
                $result->add(new ArrayCollection([$expression]));
            }
        } else {
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

    /**
     * @return PdpIntellectualRoot
     */
    public function getPdpRoot()
    {
        return $this->pdpRoot;
    }

    /**
     * @param PdpIntellectualRoot $pdpRoot
     *
     * @return PdpIntelligentSchemesGenerator
     */
    public function setPdpRoot($pdpRoot)
    {
        $this->pdpRoot = $pdpRoot;

        return $this;
    }
}