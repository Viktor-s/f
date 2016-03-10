<?php

namespace Furniture\SpecificationBundle\Exporter\Factory;

use Furniture\SpecificationBundle\Exporter\Exception\UnavailableFieldException;

class FieldMapForFactory
{
    /**
     * @var array
     */
    private static $availableFields = [
        'position',
        'factory_code',
        'type',
        'name',
        'options',
        'quantity',
        'notes',
        'price',
        'photo',
        'brand',
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
     * Has factory field
     *
     * @return bool
     */
    public function hasFieldBrand()
    {
        return in_array('brand', $this->fields);
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
     * Has field number
     *
     * @return bool
     */
    public function hasFieldNumber()
    {
        return in_array('position', $this->fields);
    }

    /**
     * Has field factory code
     *
     * @return bool
     */
    public function hasFieldFactoryCode()
    {
        return in_array('factory_code', $this->fields);
    }

    /**
     * Has field type
     *
     * @return bool
     */
    public function hasFieldType()
    {
        return in_array('type', $this->fields);
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
     * Has field options
     *
     * @return bool
     */
    public function hasFieldOptions()
    {
        return in_array('options', $this->fields);
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
     * Has field note
     *
     * @return bool
     */
    public function hasFieldNotes()
    {
        return in_array('notes', $this->fields);
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
