<?php

namespace Furniture\ProductBundle\Validator\Constraint;

use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Constraint;
use Furniture\ProductBundle\Entity\ProductVariant as ProductVariantEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ProductVariantValidator extends ConstraintValidator
{

    /**
     * {@inheritDoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$value instanceof ProductVariantEntity) {
            throw new UnexpectedTypeException($value, ProductVariantEntity::class);
        }

        if ($value->isMaster()) {
            return;
        }

        if ($value->getProduct()->isSchematicProductType() && $value->getProductScheme()) {
            $schemeParts = $value->getProductScheme()->getProductParts();
            $variantParts = new ArrayCollection();

            foreach ($value->getProductPartVariantSelections() as $index => $ppvSelection) {
                $ppvSelectionProductPart = $ppvSelection->getProductPart();
                if (!$schemeParts->contains($ppvSelectionProductPart)) {
                    $this->context->addViolationAt(
                        'productPartVariantSelections[' . $index . ']', $constraint->message, array(), null
                    );
                } else {
                    if (!$variantParts->contains($ppvSelection->getProductPart())) {
                        $variantParts->add($ppvSelection->getProductPart());
                    } else {
                        throw new \Exception(__METHOD__ . ' product variant contain one product part twice or more !', 1004);
                    }
                }
            }

            foreach ($schemeParts as $schemePart) {
                if (!$variantParts->contains($schemePart)) {
                    $this->context->addViolationAt(
                        'productPartVariantSelections', $constraint->message, array(), null
                    );
                }
            }
        }
    }

}
