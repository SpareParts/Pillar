<?php
namespace SpareParts\Pillar\Mapper;

use SpareParts\Pillar\Entity\IEntity;
use SpareParts\Pillar\Mapper\Dibi\IEntityMapping;

interface IMapper
{
	/**
	 * @param string|IEntity $classnameOrInstance
	 * @return IEntityMapping
	 * @throws EntityMappingException
	 */
	public function getEntityMapping($classnameOrInstance);
}
