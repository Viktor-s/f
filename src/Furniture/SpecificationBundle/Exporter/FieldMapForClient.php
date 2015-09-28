<?php

namespace Furniture\SpecificationBundle\Exporter;

use Furniture\SpecificationBundle\Exporter\Exception\UnavailableFieldException;

/**
 * Field map for full exports
 */
class FieldMapForClient
{
    /**
     * @var array
     */
    private static $availableFields = [
        'number',
        'factory',
        'photo',
        'name',
        'article',
        'size',
        'finishes',
        'characteristics',
        'quantity',
        'price',
        'total_price'
    ];

    /**
     * @var array
     */
    private $fields;

    /**
     * Construct
     *
     * @param array $fields
     *
     * @throws UnavailableFieldException
     */
    public function __construct(array $fields)
    {
        foreach ($fields as $field) {
            if (!in_array($field, self::$availableFields)) {
                throw new UnavailableFieldException(sprintf(
                    'The field "%s" is not available. Available fields: "%s".',
                    $field,
                    implode('", "', self::$availableFields)
                ));
            }
        }

        $this->fields = $fields;
    }

    /**
     * Has number field
     *
     * @return bool
     */
    public function hasFieldNumber()
    {
        return in_array('number', $this->fields);
    }

    /**
     * Has factory field
     *
     * @return bool
     */
    public function hasFieldFactory()
    {
        return in_array('factory', $this->fields);
    }

    /**
     * Has field photo
     *
     * @return bool
     */
    public function hasFieldPhoto()
    {
        return in_array('photo', $this->fields);
    }

    /**
     * Has field name
     *
     * @return bool
     */
    public function hasFieldName()
    {
        return in_array('name', $this->fields);
    }

    /**
     * Has field article
     *
     * @return bool
     */
    public function hasFieldArticle()
    {
        return in_array('article', $this->fields);
    }

    /**
     * Has field size
     *
     * @return bool
     */
    public function hasFieldSize()
    {
        return in_array('size', $this->fields);
    }

    /**
     * Has field finishes
     *
     * @return bool
     */
    public function hasFieldFinishes()
    {
        return in_array('finishes', $this->fields);
    }

    /**
     * Has field characteristics
     *
     * @return bool
     */
    public function hasFieldCharacteristics()
    {
        return in_array('characteristics', $this->fields);
    }

    /**
     * Has field quantity
     *
     * @return bool
     */
    public function hasFieldQuantity()
    {
        return in_array('quantity', $this->fields);
    }

    /**
     * Has field price
     *
     * @return bool
     */
    public function hasFieldPrice()
    {
        return in_array('price', $this->fields);
    }

    /**
     * Has field total price
     *
     * @return bool
     */
    public function hasFieldTotalPrice()
    {
        return in_array('total_price', $this->fields);
    }
}
