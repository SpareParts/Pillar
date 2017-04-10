<?php
namespace SpareParts\Pillar\Assistant\Dibi;

use SpareParts\Pillar\Entity\IEntity;
use SpareParts\Pillar\Mapper\Dibi\AnnotationMapper;
use SpareParts\Pillar\Mapper\Dibi\ColumnInfo;
use SpareParts\Pillar\Mapper\Dibi\TableInfo;
use SpareParts\Pillar\Mapper\EntityMappingException;

class DibiEntityAssistant
{
	/**
	 * @var AnnotationMapper
	 */
	protected $mapper;

	/**
	 * @var IConnectionProvider
	 */
	protected $connectionProvider;

	/**
	 * @var IEntityFactory
	 */
	protected $entityFactory;

	/**
	 * @var array
	 */
	private $entityPropertiesCache = [];


	/**
	 * @param AnnotationMapper $mapper
	 * @param IConnectionProvider $connectionProvider
	 * @param IEntityFactory $entityFactory
	 */
	public function __construct(AnnotationMapper $mapper, IEntityFactory $entityFactory, IConnectionProvider $connectionProvider)
	{
		$this->mapper = $mapper;
		$this->connectionProvider = $connectionProvider;
		$this->entityFactory = $entityFactory;
	}

	/**
	 * @param string|IEntity $entityClassOrInstance
	 * @return Fluent
	 * @throws \SpareParts\Pillar\Mapper\EntityMappingException
	 */
	public function fluent($entityClassOrInstance)
	{
		$fluent = new Fluent(
			$this->connectionProvider->getConnection(),
			$this->mapper->getEntityMapping($entityClassOrInstance),
			$this->entityFactory
		);
		return $fluent;
	}

	/**
	 * Update entity database representation to current entity values.
	 *
	 * @param IEntity $entity
	 * @param array|null $tables
	 *
	 * @return int Number of affected rows. Can be higher than 1, if multiple tables were changed!
	 *
	 * @throws EntityMappingException
	 * @throws UnableToSaveException
	 * @throws \DibiException
	 * @throws \InvalidArgumentException
	 */
	public function update(IEntity $entity, array $tables = null)
	{
		$mapping = $this->mapper->getEntityMapping($entity);
		$tableInfos = $mapping->getTables();
		if (!is_null($tables)) {
			$tables = array_map(function ($tableName) use ($tableInfos, $entity) {
				if (!isset($tableInfos[$tableName])) {
					throw new UnableToSaveException(sprintf('Unable to save entity: `%s`, unknown table `%s`', get_class($entity), $tableName));
				}
				return $tableInfos[$tableName];
			}, $mapping->getTables());
		}

		$affectedRows = 0;
		foreach ($tableInfos as $tableInfo) {
			// we need array key of ColumnInfo[] to be the column name for easy handling
			/** @var ColumnInfo[] $columns */
			$columns = [];
			foreach ($mapping->getColumnsForTable($tableInfo->getIdentifier()) as $columnInfo) {
				$columns[$columnInfo->getPropertyName()] = $columnInfo;
			}
			// properties/columns that changed in this table
			$changedProperties = $entity->getChangedProperties(array_keys($columns));

			$columnValuesToStore = [];
			foreach ($changedProperties as $changedProperty => $newPropertyValue) {
				// sanity check: is changed property in the $tableName table?
				if (!isset($columns[$changedProperty])) {
					continue;
				}
				// we are not updating primary keys of tables - this should be done by inserting new record and changing linked rows
				if ($columns[$changedProperty]->isPrimaryKey()) {
					continue;
				}

				$columnName = $columns[$changedProperty]->getColumnName();
				$columnValuesToStore[$columnName] = $newPropertyValue;
			}
			// nothing to update in this table
			if (!count($columnValuesToStore)) {
				continue;
			}

			$fluent = $this->connectionProvider->getConnection()
				->update($tableInfo->getName(), $columnValuesToStore)
				->limit(1);

			$fluent = $this->addPKToFluent($entity, $tableInfo->getIdentifier(), $columns, $fluent);
			$affectedRows += $fluent->execute(\dibi::AFFECTED_ROWS);
		}
		return $affectedRows;
	}

	/**
	 * @param IEntity $entity
	 * @param string $tableName
	 *
	 * @return int|string|null
	 *
	 * @throws EntityMappingException
	 * @throws \DibiException
	 * @throws \InvalidArgumentException
	 */
	public function insert(IEntity $entity, $tableName)
	{
		$mapping = $this->mapper->getEntityMapping($entity);
		$tableInfos = $mapping->getTables();
		if (!isset($tableInfos[$tableName])) {
			throw new UnableToSaveException(sprintf('Unable to save entity: `%s`, unknown table `%s`', get_class($entity), $tableName));
		}
		$tableInfo = $tableInfos[$tableName];

		$changedProperties = $entity->getChangedProperties($this->getEntityProperties($mapping->getEntityClassName()));
		// we need array key of ColumnInfo[] to be the column name for easy handling
		/** @var ColumnInfo[] $columns */
		$columns = [];
		foreach ($mapping->getColumnsForTable($tableName) as $columnInfo) {
			$columns[$columnInfo->getPropertyName()] = $columnInfo;
		}

		$columnValuesToStore = [];
		foreach ($changedProperties as $changedProperty => $newPropertyValue) {
			// is changed property in $tableName table?
			if (!isset($columns[$changedProperty])) {
				continue;
			}

			$columnName = $columns[$changedProperty]->getColumnName();
			$columnValuesToStore[$columnName] = $newPropertyValue;
		}
		// nothing to store in this table
		if (!count($columnValuesToStore)) {
			return null;
		}

		$fluent = $this->connectionProvider->getConnection()
			->insert($tableInfo->getName(), $columnValuesToStore);

		return $fluent->execute(\dibi::IDENTIFIER);
	}

	/**
	 * @param IEntity $entity
	 * @param $tableName
	 *
	 * @return int
	 *
	 * @throws EntityMappingException
	 * @throws UnableToSaveException
	 * @throws \DibiException
	 */
	public function delete(IEntity $entity, $tableName)
	{
		$mapping = $this->mapper->getEntityMapping($entity);
		$tableInfos = $mapping->getTables();
		if (!isset($tableInfos[$tableName])) {
			throw new UnableToSaveException(sprintf('Unable to save entity: `%s`, unknown table `%s`', get_class($entity), $tableName));
		}
		$tableInfo = $tableInfos[$tableName];

		$fluent = $this->connectionProvider->getConnection()
			->delete($tableInfo->getName())
			->limit(1);

		// we need array key of ColumnInfo[] to be the column name for easy handling
		/** @var ColumnInfo[] $columns */
		$columns = [];
		foreach ($mapping->getColumnsForTable($tableName) as $columnInfo) {
			$columns[$columnInfo->getPropertyName()] = $columnInfo;
		}
		$fluent = $this->addPKToFluent($entity, $tableInfo->getName(), $columns, $fluent);

		return $fluent->execute(\dibi::AFFECTED_ROWS);
	}

	/**
	 * @param IEntity $entity
	 * @param string $tableName
	 * @param ColumnInfo[] $columns
	 * @param \DibiFluent $fluent
	 *
	 * @return \DibiFluent
	 *
	 * @throws UnableToSaveException
	 */
	private function addPKToFluent(IEntity $entity, $tableName, $columns, \DibiFluent $fluent)
	{
		/** @var ColumnInfo[] $pkColumns */
		$pkColumns = array_filter($columns, function (ColumnInfo $columnInfo) {
			return $columnInfo->isPrimaryKey();
		});
		if (!count($pkColumns)) {
			throw new UnableToSaveException(sprintf('No primary key exists for table %s of entity %s', $tableName,
				get_class($entity)));
		}
		// all columns marked as "primary" are considered to be a part of primary key
		foreach ($pkColumns as $pkColumn) {
			$pkProperty = $columns[ $pkColumn->getPropertyName() ]->getPropertyName();
			$getterPk = \Closure::bind(function () use ($pkProperty) {
				return $this->{$pkProperty};
			}, $entity, get_class($entity));
			$pkValue = $getterPk();
			if (!is_scalar($pkValue)) {
				throw new UnableToSaveException(sprintf('Entity: `%s` should have its table: `%s` saved, but primary key column\'s : `%s` value is not a scalar (%s),', get_class($entity), $tableName, $pkColumn->getColumnName(), print_r($pkValue, true)));
			}

			$fluent->where(
				'%n = %i',
				$pkColumn->getColumnName(),
				$pkValue
			);
		}
		return $fluent;
	}

	/**
	 * @param $entityClassName
	 * @return array
	 * @throws \SpareParts\Pillar\Mapper\EntityMappingException
	 */
	private function getEntityProperties($entityClassName)
	{
		if (!isset($this->entityPropertiesCache[$entityClassName])) {
			$mapping = $this->mapper->getEntityMapping($entityClassName);
			$tables = $mapping->getTables();
			$properties = [];
			foreach ($tables as $tableInfo) {
				$columns = $mapping->getColumnsForTable($tableInfo->getIdentifier());
				foreach ($columns as $columnInfo) {
					$properties[] = $columnInfo->getPropertyName();
				}
			}
			$this->entityPropertiesCache[$entityClassName] = $properties;
		}
		return $this->entityPropertiesCache[$entityClassName];
	}
}
