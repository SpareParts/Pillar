<?php
namespace SpareParts\Pillar\Entity;

interface IEntity
{
	/**
	 * @param string[] $properties List of concerned properties
	 *
	 * @return mixed[] Named list in the form of [property_name => new_property_value]
	 */
	public function getChangedProperties($properties);
}
