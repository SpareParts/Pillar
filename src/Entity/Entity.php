<?php
namespace SpareParts\Pillar\Entity;

/**
 * Simple implementation of IEntity interface
 * Can be used as an example for your own, specific implementation
 * This is a bit reliant on entity factory used (factory calls contructor, so the format of the constructor must be the same as the factory uses)
 */
class Entity implements IEntity
{
	/**
	 * @var mixed[]
	 */
	private $_originalValues;

	/**
	 * @param array $data
	 */
	public function __construct(array $data = []) {
		$this->_originalValues = $data;
		foreach ($data as $propertyName => $propertyValue) {
			$this->{$propertyName} = $propertyValue;
		}
	}

	/**
	 * @param string[] $properties List of concerned properties
	 * @return mixed[]
	 */
	public function getChangedProperties($properties)
	{
		$changed = [];
		foreach ($properties as $property) {
			$originalValue = isset($this->_originalValues[$property]) ? $this->_originalValues[$property] : null;
			if ($originalValue !== $this->{$property}) {
				$changed[$property] = $this->{$property};
			}
		}
		return $changed;
	}
}
