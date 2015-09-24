<?php

namespace Furniture\SpecificationBundle\Controller\Api;

use Symfony\Component\Form\FormInterface;

trait FormErrorsTrait
{
    /**
     * Convert form errors to array
     *
     * @param FormInterface $form
     *
     * @return array
     */
    protected function convertFormErrorsToArray(FormInterface $form)
    {
        $errors = [];

        /** @var FormInterface $childForm */
        foreach ($form as $childForm) {
            $path = $childForm->getName();

            if (count($formErrors = $childForm->getErrors()) > 0) {
                foreach ($formErrors as $error) {
                    if (!isset($errors[$path])) {
                        $errors[$path] = [];
                    }

                    $errors[$path][] = $error->getMessage();
                }
            }

            if (count($childForm) > 0) {
                $childErrors = $this->convertFormErrorsToArray($childForm);

                if (count($childErrors)) {
                    $errors[$path] = $childErrors;
                }
            }
        }

        return $errors;
    }
}
