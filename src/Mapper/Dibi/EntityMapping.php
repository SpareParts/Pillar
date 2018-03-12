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
	 * @var bool
	 */
	private $isVirtualEntity;

	/**
	 * @param string $entityClassName
	 * @param TableInfo[] $tableInfoList
	 * @param ColumnInfo[] $columnInfoList
	 * @param bool $isVirtualEntity
	 */
	public function __construct($entityClassName, array $tableInfoList, array $columnInfoList, $isVirtualEntity = false)
	{
		$this->tableInfoList = $tableInfoList;
		$this->columnInfoList = $columnInfoList;
		$this->entityClassName = $entityClassName;
		$this->isVirtualEntity = $isVirtualEntity;
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

	/**
	 * @return bool
	 */
	public  function isVirtualEntity() {
		return $this->isVirtualEntity;
	}
}
