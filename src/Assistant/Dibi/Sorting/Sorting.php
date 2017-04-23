<?php
namespace SpareParts\Pillar\Assistant\Dibi\Sorting;

class Sorting implements ISorting
{
	/**
	 * @var string
	 */
	private $property;

	/**
	 * @var SortingDirectionEnum
	 */
	private $direction;

	/**
	 * @param string $property
	 * @param SortingDirectionEnum $direction
	 */
	public function __construct($property, SortingDirectionEnum $direction)
	{
		$this->property = $property;
		$this->direction = $direction;
	}

	/**
	 * @return string
	 */
	public function getProperty()
	{
		return $this->property;
	}

	/**
	 * @return SortingDirectionEnum
	 */
	public function getDirection()
	{
		return $this->direction;
	}
}
