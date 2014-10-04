<?php

/**
 * This file is part of the Nextras\ORM library.
 * This file was inspired by PetrP's ORM library https://github.com/PetrP/Orm/.
 *
 * @license    MIT
 * @link       https://github.com/nextras/orm
 * @author     Jan Skrasek
 */

namespace Nextras\Orm\Entity;

use Nextras\Orm\Entity\Reflection\PropertyMetadata;
use Nextras\Orm\Relationships\IRelationshipCollection;
use Nextras\Orm\Relationships\IRelationshipContainer;


class PersistanceHelper
{

	/**
	 * Returns entity relationships as array, 0 => prePersist, 1 => postPersist
	 * @param  IEntity  $entity
	 * @return array
	 */
	public static function getLoadedRelationships(IEntity $entity)
	{
		$return = [[], []];
		foreach ($entity->getMetadata()->getProperties() as $propertyMeta) {
			if (!$propertyMeta->relationshipType) {
				continue;
			}

			$name = $propertyMeta->name;
			$property = $entity->getProperty($name);
			if ($property instanceof IRelationshipContainer) {
				if (!$property->isLoaded()) {
					continue;
				}

				$value = $entity->getValue($name);
				if ($value) {
					if ($propertyMeta->relationshipType === PropertyMetadata::RELATIONSHIP_ONE_HAS_ONE_DIRECTED && !$propertyMeta->relationshipIsMain) {
						$return[1][$name] = $value;
					} else {
						$return[0][$name] = $value;
					}
				}

			} elseif ($property instanceof IRelationshipCollection) {
				if (!$property->isLoaded()) {
					continue;
				}

				$value = $entity->getValue($name);
				if ($value) {
					$return[1][$name] = $value;
				}
			}
		}

		return $return;
	}


	public static function toArray(IEntity $entity)
	{
		$return = [];
		$metadata = $entity->getMetadata();

		foreach ($metadata->getStorageProperties() as $name) {
			if ($name === 'id' && !$entity->isPersisted()) {
				$value = NULL;

			} else {
				$property = $entity->getProperty($name);
				if ($property instanceof IRelationshipContainer) {
					if (!$property->isLoaded()) {
						$value = $property->getPrimaryValue();
					} else {
						$value = $entity->getValue($name);
					}
				} elseif ($property instanceof IRelationshipCollection) {
					if (!$property->isLoaded()) {
						continue;
					} else {
						$value = $entity->getValue($name);
					}
				} else {
					$value = $entity->getValue($name);
				}
			}

			$return[$name] = $value;
		}

		return $return;
	}

}
