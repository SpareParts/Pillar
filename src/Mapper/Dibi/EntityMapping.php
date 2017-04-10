<?php
namespace SpareParts\Pillar\Mapper\Dibi;

class EntityMapping implements IEntityMapping
{
	/**
	 * @var TableInfo[]
	 */
	private $tableInfoList = [];

	/**
	 * @var ColumnInfo[]
	 */
	private $columnInfoList = [];

	/**
	 * @var string
	 */
	private $entityClassName;

	/**
	 * @param string $entityClassName
	 * @param TableInfo[] $tableInfoList
	 * @param ColumnInfo[] $columnInfoList
	 */
	public function __construct($entityClassName, array $tableInfoList, array $columnInfoList)
	{
		$this->tableInfoList = $tableInfoList;
		$this->columnInfoList = $columnInfoList;
		$this->entityClassName = $entityClassName;
	}

	/**
	 * @return string
	 */
	public function getEntityClassName()
	{
		return $this->entityClassName;
	}

	/**
	 * @return TableInfo[]
	 */
	public function getTables()
	{
		return $this->tableInfoList;
	}

	/**
	 * @param string $tableIdentifier
	 * @return ColumnInfo[]
	 */
	public function getColumnsForTable($tableIdentifier)
	{
		return array_filter($this->columnInfoList, function (ColumnInfo $columnInfo) use ($tableIdentifier) {
			return ($columnInfo->getTableInfo()->getIdentifier() === $tableIdentifier);
		});
	}
}
