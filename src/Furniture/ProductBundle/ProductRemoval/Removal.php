<?php

namespace Furniture\ProductBundle\ProductRemoval;

abstract class Removal
{
    /**
     * @var bool
     */
    private $canRemove;

    /**
     * @var array|string[]
     */
    private $reasonMessages;

    /**
     * Construct
     *
     * @param bool  $canRemove
     * @param array $reasonMessages
     */
    public function __construct($canRemove, array $reasonMessages = null)
    {
        $this->canRemove = $canRemove;
        $this->reasonMessages = $reasonMessages;
    }

    /**
     * Can remove?
     *
     * @return bool
     */
    public function canRemove()
    {
        return $this->canRemove;
    }

    /**
     * Not can remove?
     *
     * @return bool
     */
    public function notCanRemove()
    {
        return !$this->canRemove;
    }

    /**
     * Get reason
     *
     * @return array|string[]|null
     */
    public function getReasonMessages()
    {
        return $this->reasonMessages;
    }

    /**
     * Implement __toString
     *
     * @return string
     */
    public function __toString()
    {
        $messages = array_map(function ($str) {
            return trim($str, '.');
        }, $this->reasonMessages);

        return implode('. ', $messages);
    }
}