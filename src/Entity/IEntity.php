<?php
namespace SpareParts\Pillar\Entity;

interface IEntity
{
	/**
	 * @param string[] $properties List of concerned properties
	 *
	 * @return string[]
	 */
	public function getChangedProperties($properties);
}
