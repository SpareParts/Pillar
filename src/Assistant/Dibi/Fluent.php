<?php
namespace SpareParts\Pillar\Assistant\Dibi;

use SpareParts\Enum\Converter\MapConverter;
use SpareParts\Pillar\Assistant\Dibi\Sorting\ISorting;
use SpareParts\Pillar\Assistant\Dibi\Sorting\SortingDirectionEnum;
use SpareParts\Pillar\Mapper\Dibi\ColumnInfo;
use SpareParts\Pillar\Mapper\Dibi\IEntityMapping;

class Fluent extends \DibiFluent
{
	/**
	 * @var IEntityMapping
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

	/**
	 * @param ISorting[] $sortingList
	 * @return $this
	 * @throws UnknownPropertyException
	 * @throws \SpareParts\Enum\Converter\UnableToConvertException
	 */
	public function applySorting(array $sortingList)
	{
		if (count($sortingList) === 0) {
			// don't try to apply empty $sortingList
			return $this;
		}

		/** @var ColumnInfo[] $sortableProperties */
		$sortableProperties = [];
		foreach ($this->entityMapping->getTables() as $tableInfo) {
			foreach ($this->entityMapping->getColumnsForTable($tableInfo->getIdentifier()) as $columnInfo) {
				if (isset($sortableProperties[$columnInfo->getPropertyName()])) {
					continue;
				}
				$sortableProperties[$columnInfo->getPropertyName()] = $columnInfo;
			}
		}
		$directionMap = new MapConverter([
			'ASC' => SortingDirectionEnum::ASCENDING(),
			'DESC' => SortingDirectionEnum::DESCENDING(),
		]);

		foreach ($sortingList as $sorting) {
			if (!isset($sortableProperties[$sorting->getProperty()])) {
				throw new UnknownPropertyException(sprintf('Unable to map property: `%s` to entity: `%s`, please check whether the provided property name is correct.', $sorting->getProperty(), $this->entityMapping->getEntityClassName()));
			}
			$columnInfo = $sortableProperties[$sorting->getProperty()];
			$this->orderBy(
				'%n', sprintf(
					'%s.%s',
					$columnInfo->getTableInfo()->getIdentifier(),
					$columnInfo->getColumnName()
				),
				$directionMap->fromEnum($sorting->getDirection())
			);
		}
		return $this;
	}
}
