<?php
namespace SpareParts\Pillar\Assistant\Dibi\Sorting;

interface ISorting
{
	/**
	 * @return string
	 */
	public function getProperty();

	/**
	 * @return SortingDirectionEnum
	 */
	public function getDirection();
}
