<?php
/**
 * Created by Shtompel Konstantin.
 * User: synthetic
 * Date: 3/23/2016
 * Time: 11:55 AM
 */

namespace Furniture\PostgresSearchBundle\DBAL;


use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class TsvectorType extends Type
{
    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValueSQL($sqlExpr, AbstractPlatform $platform)
    {
        return sprintf('to_tsvector(%s)', $sqlExpr);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        $result = '';

        if (is_array($value)) {
            foreach ($value as $item) {
                if (is_array($item)) {
                    $item = implode(' ', $item);
                }
                $result .= $item . ' ';
            }
        }
        else if (is_string($value)) {
            $result = $value;
        }

        $result = trim($result);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function canRequireSQLConversion()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return 'tsvector';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'tsvector';
    }
}