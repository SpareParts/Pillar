<?php
namespace SpareParts\Pillar\Assistant\Dibi;

use SpareParts\Pillar\Entity\IEntity;

interface IEntityFactory
{
	/**
	 * @param string $entityClassName
	 * @param mixee[] $data
	 * @return IEntity
	 */
	public function createEntity($entityClassName, array $data);
}
