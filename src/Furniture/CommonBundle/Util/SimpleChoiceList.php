<?php

namespace Furniture\CommonBundle\Util;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class SimpleChoiceList extends AbstractChoiceList
{
    /**
     * @var array
     */
    private $preferredItems = [];

    /**
     * @var array
     */
    private $groupPreferredItems = [];

    /**
     * @var boolean
     */
    private $sorted = false;

    /**
     * {@inheritdoc}
     */
    public function setOptions($options)
    {
        parent::setOptions($options);

        if (!empty($options['preferred_items'])) {
            $this->setPreferredItems($options['preferred_items']);
        }
    }

    /**
     * @return boolean
     */
    public function isSorted()
    {
        return $this->sorted;
    }

    /**
     * @param boolean $sorted
     * @return SimpleChoiceList
     */
    public function setSorted($sorted)
    {
        $this->sorted = $sorted;

        return $this;
    }


    /**
     * Get preferred items.
     *
     * @return array
     */
    public function getPreferredItems()
    {
        return $this->preferredItems;
    }

    /**
     * Set preferred items.
     *
     * @param array $preferredItems
     * @return SimpleChoiceList
     */
    public function setPreferredItems(array $preferredItems)
    {
        $this->preferredItems = $preferredItems;

        return $this;
    }

    /**
     * Add preferred items.
     *
     * @param mixed $items
     * @return SimpleChoiceList
     */
    public function addPreferredItems($items)
    {
        if (is_array($items)) {
            $this->preferredItems += $items;
        } else {
            $this->preferredItems[] = $items;
        }

        return $this;
    }

    /**
     * Get group preferred items.
     *
     * @return array
     */
    public function getGroupPreferredItems()
    {
        return $this->groupPreferredItems;
    }

    /**
     * Set group preferred items.
     *
     * @param array $groupPreferredItems
     * @return SimpleChoiceList
     */
    public function setGroupPreferredItems(array $groupPreferredItems)
    {
        $this->groupPreferredItems = $groupPreferredItems;

        return $this;
    }

    /**
     * Add group preferred items.
     *
     * @param mixed  $items
     * @param string $groupName
     * @return SimpleChoiceList
     */
    public function addGroupPreferredItems($items, $groupName)
    {
        if (is_array($items)) {
            if ($this->has($groupName)) {
                $this->groupPreferredItems[$groupName] += $items;
            } else {
                $this->groupPreferredItems[$groupName] = $items;
            }
        } else {
            if ($this->has($groupName)) {
                $this->groupPreferredItems[$groupName][] = $items;
            } else {
                $this->groupPreferredItems[$groupName] = [$items];
            }
        }

        return $this;
    }

    /**
     * Check for preferred items.
     *
     * @return bool
     */
    public function hasPreferred()
    {
        return !empty($this->preferredItems) || !empty($this->groupPreferredItems);
    }

    /**
     * Sort preferred choices.
     */
    private function sortByPreferred()
    {
        if (!$this->sorted && $this->hasPreferred()) {
            /** @var ArrayCollection[] $partition */
            $partition = $this->partition(
                function ($key, $value) {
                    return !is_array($value) && !$value instanceof Collection;
                }
            );

            $notGrouped = $partition[0]->toArray();
            $grouped = $partition[1]->toArray();

            if (!empty($this->preferredItems)) {
                uksort($notGrouped, [$this, 'sortPreferred']);
            }

            /** @var ArrayCollection $value */
            foreach ($grouped as $key => &$value) {
                if (!empty($this->groupPreferredItems[$key])) {
                    $value = $value->toArray();
                    $groupPreferredSort = $this->sortGroupPreferred($this->groupPreferredItems[$key]);
                    /** @var array $value */
                    uksort($value, $groupPreferredSort);
                    $value = new ArrayCollection($value);
                }
            }
            $elements = array_merge($notGrouped, $grouped);
            $this->setChoices($elements);
            $this->sorted = true;
        }
    }

    /**
     * Sort items by preferred.
     *
     * @param $key
     * @param $nextKey
     * @return int|mixed
     */
    private function sortPreferred($key, $nextKey)
    {
        if ($this->valueInPreferred($key) && !$this->valueInPreferred($nextKey)) {
            return -1;
        } else if ($this->valueInPreferred($key) && $this->valueInPreferred($nextKey)) {
            return array_search($key, $this->preferredItems) - array_search($nextKey, $this->preferredItems);
        }

        return 1;
    }

    /**
     * Sort items by preferred in groups.
     *
     * @param array $preferred
     * @return \Closure
     */
    private function sortGroupPreferred(array $preferred)
    {
        return function ($key, $nextKey) use ($preferred) {
            if (in_array($key, $preferred) && !in_array($nextKey, $preferred)) {
                return -1;
            } else if (in_array($key, $preferred) && in_array($nextKey, $preferred)) {
                return array_search($key, $preferred) - array_search($nextKey, $preferred);
            }

            return 1;
        };
    }

    /**
     * Check that value in preferred.
     *
     * @param $value
     * @return bool
     */
    private function valueInPreferred($value)
    {
        return in_array($value, $this->preferredItems);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        $this->sortByPreferred();

        return $this->choices->getIterator();
    }
}
