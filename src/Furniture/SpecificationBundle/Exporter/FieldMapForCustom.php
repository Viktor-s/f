<?php

namespace Furniture\SpecificationBundle\Exporter;

use Furniture\SpecificationBundle\Exporter\Exception\UnavailableFieldException;

class FieldMapForCustom
{
    /**
     * @var array
     */
    private static $availableFields = [
        'number',
        'photo',
        'name',
        'quantity'
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
     * Has field number
     *
     * @return bool
     */
    public function hasFieldNumber()
    {
        return in_array('number', $this->fields);
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
     * Has field quantity
     *
     * @return bool
     */
    public function hasFieldQuantity()
    {
        return in_array('quantity', $this->fields);
    }
}
