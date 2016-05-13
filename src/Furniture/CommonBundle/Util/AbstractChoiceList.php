<?php

namespace Furniture\CommonBundle\Util;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

abstract class AbstractChoiceList implements \IteratorAggregate
{
    /**
     * @var ArrayCollection
     */
    protected $choices;

    /**
     * @var string
     */
    protected $selectedItem;

    /**
     * @var boolean
     */
    protected $hasGroups = false;

    /**
     * SimpleChoiceList constructor.
     *
     * @var array|ArrayCollection $choices
     * @var array                 $options
     */
    public function __construct($choices = [], $options = [])
    {
        $this->setChoices($choices);
        $this->setOptions($options);
    }

    /**
     * Set options.
     *
     * @param $options
     */
    public function setOptions($options)
    {
        if (!empty($options['selected'])) {
            $this->setSelectedItem($options['selected']);
        }
    }

    /**
     * @param array|ArrayCollection $choices
     * @param array                 $options
     * @return SimpleChoiceList
     * @throws \Exception
     */
    public function addChoices($choices, array $options = [])
    {
        if (!is_array($choices) && !$choices instanceof Collection) {
            throw new \Exception(sprintf('Choices should be instance of %s or array', Collection::class));
        }

        $choices = (array)$choices;
        foreach ($choices as $key => $value) {
            if (is_array($value) || $value instanceof Collection) {
                $this->addChoices($value, ['group' => $key]);
            } else {
                $this->addChoice($key, $value, $options);
            }
        }

        return $this;
    }

    /**
     * @param       $key
     * @param       $value
     * @param array $options
     *
     * @return SimpleChoiceList
     */
    public function addChoice($key, $value, array $options = [])
    {
        if (!empty($options['group'])) {
            $this->addGroup($options['group']);
            $this->get($options['group'])->set($key, $value);
        } else {
            $this->set($key, $value);
        }

        return $this;
    }

    /**
     * @param string $groupName
     *
     * @return SimpleChoiceList
     */
    public function addGroup($groupName)
    {
        if (!empty($groupName) && !$this->choices->containsKey($groupName)) {
            $this->set($groupName, new ArrayCollection());
            if (!$this->isHasGroups()) {
                $this->setHasGroups(true);
            }
        }

        return $this;
    }

    /**
     * @return boolean
     */
    public function isHasGroups()
    {
        return $this->hasGroups;
    }

    /**
     * @param boolean $hasGroups
     * @return SimpleChoiceList
     */
    public function setHasGroups($hasGroups)
    {
        $this->hasGroups = $hasGroups;

        return $this;
    }

    /**
     * @return string
     */
    public function getSelectedItem()
    {
        return $this->selectedItem;
    }

    /**
     * @param string $selectedItem
     * @return SimpleChoiceList
     */
    public function setSelectedItem($selectedItem)
    {
        $this->selectedItem = $selectedItem;

        return $this;
    }

    /**
     * Check that value is selected item.
     *
     * @param $selectedItem
     * @return bool
     */
    public function isSelectedItem($selectedItem)
    {
        return strcmp($this->selectedItem, $selectedItem) === 0;
    }

    /**
     * Return elements as array.
     *
     * @return ArrayCollection
     */
    public function getChoices()
    {
        return $this->toArray();
    }

    /**
     * Set elements.
     *
     * @param array|ArrayCollection $choices
     * @return SimpleChoiceList
     */
    public function setChoices($choices)
    {
        if (is_array($choices)) {
            $choices = new ArrayCollection($choices);
        }
        $this->choices = $choices;
        $this->checkGroups();

        return $this;
    }

    /**
     * Check for the groups.
     */
    protected function checkGroups()
    {
        $this->choices = $this->map(
            function ($value) {
                return is_array($value) ? new ArrayCollection($value) : $value;
            }
        );

        $exists = $this->exists(
            function ($key, $value) {
                return $value instanceof Collection;
            }
        );

        $this->setHasGroups($exists);
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public function has($value)
    {
        return $this->choices->contains($value);
    }

    /**
     * @param string|integer $key
     * @return bool
     */
    public function hasKey($key)
    {
        return $this->choices->containsKey($key);
    }

    /**
     * @param string|integer $key
     * @return mixed|null
     */
    public function get($key)
    {
        return $this->choices->get($key);
    }

    /**
     * @return array
     */
    public function getKeys()
    {
        return $this->choices->getKeys();
    }

    /**
     * @return array
     */
    public function getValues()
    {
        return $this->choices->getValues();
    }

    /**
     * @param string|integer $key
     * @param mixed          $value
     */
    public function set($key, $value)
    {
        $this->choices->set($key, $value);
    }

    /**
     * @return int
     */
    public function count()
    {
        return $this->choices->count();
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return $this->choices->isEmpty();
    }

    /**
     * @param \Closure $p
     * @return bool
     */
    public function exists(\Closure $p)
    {
        return $this->choices->exists($p);
    }

    /**
     * @param \Closure $p
     * @return array
     */
    public function partition(\Closure $p)
    {
        return $this->choices->partition($p);
    }

    /**
     * @param \Closure $p
     * @return Collection|static
     */
    public function map(\Closure $p)
    {
        return $this->choices->map($p);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->choices->toArray();
    }

    /**
     * {@inheritdoc}
     */
    abstract function getIterator();
}
