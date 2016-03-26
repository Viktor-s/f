<?php
/**
 * Created by Shtompel Konstantin.
 * User: synthetic
 * Date: 3/23/2016
 * Time: 11:47 AM
 */

namespace Furniture\PostgresSearchBundle\EventListener;

use Doctrine\Common\Annotations\AnnotationException;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Furniture\ProductBundle\Entity\ProductTranslation;

class SearchListener
{
    public function onFlush(OnFlushEventArgs $args)
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityInsertions() as $entity) {
            $metadata = $em->getClassMetadata(get_class($entity));
            foreach ($metadata->getFieldNames() as $field) {
                if ($metadata->getTypeOfField($field) != 'tsvector') {
                    continue;
                }

                $fieldMapping = $metadata->getFieldMapping($field);

                if (!isset($fieldMapping['options']['searchOptions']['searchFields'])) {
                    continue;
                }

                $searchFields = $fieldMapping['options']['searchOptions']['searchFields'];

                $locale = null;
                $defaultLanguage = null;

                if (isset($fieldMapping['options']['searchOptions']['defaultLanguage'])) {
                    $defaultLanguage = $fieldMapping['options']['searchOptions']['defaultLanguage'];
                }

                if (isset($fieldMapping['options']['searchOptions']['localeField'])) {
                    $localeField = $fieldMapping['options']['searchOptions']['localeField'];
                    if (in_array($localeField, $metadata->getFieldNames())) {
                        $getter = 'get'.ucfirst($localeField);
                        if (!method_exists($entity, $getter)) {
                            throw new AnnotationException(
                                'Getter '.$getter.' for locale field does not exists.'
                            );
                        }
                        if (isset($fieldMapping['options']['searchOptions']['languageMapping'])
                            && in_array($entity->$getter(), $fieldMapping['options']['searchOptions']['languageMapping'])) {
                            $locale = $fieldMapping['options']['searchOptions']['languageMapping'][$entity->$getter()];
                        } else {
                            $locale = $defaultLanguage;
                        }
                    }
                }

                $searchData = [];

                if ($locale) {
                    $searchData['locale'] = [$locale];
                }

                foreach ($searchFields as $searchField) {
                    $getter = 'get'.ucfirst($searchField);

                    if (!method_exists($entity, $getter)) {
                        throw new AnnotationException(
                            'Getter '.$getter.' for search field does not exists.'
                        );
                    }

                    $searchData[] = $entity->$getter();
                }

                $metadata->setFieldValue($entity, $field, $searchData);
                $uow->recomputeSingleEntityChangeSet($metadata, $entity);
            }
        }

        foreach ($uow->getScheduledEntityUpdates() as $entity) {

            $changeSet = $uow->getEntityChangeSet($entity);
            $metadata = $em->getClassMetadata(get_class($entity));

            foreach ($metadata->getFieldNames() as $field) {
                if ($metadata->getTypeOfField($field) != 'tsvector') {
                    continue;
                }

                $fieldMapping = $metadata->getFieldMapping($field);
                if (!isset($fieldMapping['options']['searchOptions']['searchFields'])) {
                    continue;
                }

                $updateNeeded = false;
                $searchFields = $fieldMapping['options']['searchOptions']['searchFields'];

                if (isset($fieldMapping['options']['searchOptions']['triggerRecompute'])) {
                    $triggerRecompute = $fieldMapping['options']['searchOptions']['triggerRecompute'];
                } else {
                    $triggerRecompute = [];
                }

                foreach ($changeSet as $fieldName => $value) {
                    if (in_array($fieldName, $searchFields) || in_array($fieldName, $triggerRecompute)) {
                        $updateNeeded = true;
                        break;
                    }
                }

                if (!$updateNeeded) {
                    continue;
                }

                $locale = null;
                $defaultLanguage = null;

                if (isset($fieldMapping['options']['searchOptions']['defaultLanguage'])) {
                    $defaultLanguage = $fieldMapping['options']['searchOptions']['defaultLanguage'];
                }

                if (isset($fieldMapping['options']['searchOptions']['localeField'])) {
                    $localeField = $fieldMapping['options']['searchOptions']['localeField'];
                    if (in_array($localeField, $metadata->getFieldNames())) {
                        $getter = 'get'.ucfirst($localeField);
                        if (!method_exists($entity, $getter)) {
                            throw new AnnotationException(
                                'Getter '.$getter.' for locale field does not exists.'
                            );
                        }
                        if (isset($fieldMapping['options']['searchOptions']['languageMapping'])
                            && in_array($entity->$getter(), $fieldMapping['options']['searchOptions']['languageMapping'])) {
                            $locale = $fieldMapping['options']['searchOptions']['languageMapping'][$entity->$getter()];
                        } else {
                            $locale = $defaultLanguage;
                        }
                    }
                }

                $searchData = [];

                if ($locale) {
                    $searchData['locale'] = [$locale];
                }

                foreach ($searchFields as $searchField) {
                    $getter = 'get'.ucfirst($searchField);

                    if (!method_exists($entity, $getter)) {
                        throw new AnnotationException(
                            'Getter '.$getter.' for search field does not exists.'
                        );
                    }
                    $searchData[] = $entity->$getter();
                }

                $metadata->setFieldValue($entity, $field, $searchData);
                $uow->recomputeSingleEntityChangeSet($metadata, $entity);
            }
        }
    }
}
