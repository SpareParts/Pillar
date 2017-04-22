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
		$propertyList = [];
		foreach ($this->entityMapping->getTables() as $table) {
			foreach ($this->entityMapping->getColumnsForTable($table->getIdentifier()) as $column) {
				// each property may be mapped to multiple columns, we are using mapping to the FIRST ACTIVE table and ignoring the rest
				if (isset($propertyList[$column->getPropertyName()])) {
					continue;
				}
				$propertyList[$column->getPropertyName()] = true;

				if ($column->getCustomSelectSql()) {
					$this->select($column->getCustomSelectSql())->as($column->getPropertyName());
				} else {
					$this->select('%n', sprintf(
						'%s.%s',
						$column->getTableInfo()->getIdentifier(),
						$column->getColumnName()
					))->as($column->getPropertyName());
				}
			}
		}
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
}
