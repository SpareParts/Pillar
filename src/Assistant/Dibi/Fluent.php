<?php
namespace SpareParts\Pillar\Assistant\Dibi;

use SpareParts\Pillar\Entity\IEntity;
use SpareParts\Pillar\Mapper\Dibi\IEntityMapping;

class Fluent extends \DibiFluent
{
	/**
	 * @var \SpareParts\Pillar\Mapper\IEntityMapping
	 */
	protected $entityMapping;

	/**
	 * @var IEntityFactory
	 */
	protected $entityFactory;

	public function __construct(\DibiConnection $connection, IEntityMapping $entityMapping, IEntityFactory $entityFactory = null)
	{
		parent::__construct($connection);
		if ($entityFactory) {
			$this->setupResult('setRowFactory', function (array $data) use ($entityFactory, $entityMapping) {
				return $entityFactory->createEntity($entityMapping->getEntityClassName(), $data);
			});
		}

		$this->entityMapping = $entityMapping;
		$this->entityFactory = $entityFactory;
	}

	/**
	 * @return $this
	 */
	public function selectEntityProperties()
	{
		$selectFieldList = [];
		foreach ($this->entityMapping->getTables() as $table) {
			foreach ($this->entityMapping->getColumnsForTable($table->getIdentifier()) as $column) {
				if (!isset($selectFieldList[$column->getPropertyName()])) {
					$selectFieldList[$column->getPropertyName()] = sprintf(
						'%s.%s',
						$column->getTableInfo()->getIdentifier(),
						$column->getColumnName()
					);
				}
			}
		}
		$this->select(array_flip($selectFieldList));
		return $this;
	}

	/**
	 * @return $this
	 */
	public function fromEntityDataSources()
	{
		$tables = $this->entityMapping->getTables();

		$primaryTable = array_shift($tables);
		$this->from(sprintf(
			'`%s` AS `%s`',
			$primaryTable->getName(),
			$primaryTable->getIdentifier()
		));

		foreach ($tables as $table) {
			$this->__call('', [$table->getSqlJoinCode()]);
		}
		return $this;
	}


	public function setSorting(ISorting ...$sorting)
	{

	}

	/**
	 * @param array $data
	 * @return IEntity
	 */
	public function rowClassFactory($data)
	{
		if ($this->entityFactory) {

		}

		// fallback
		return $data;
	}
}
