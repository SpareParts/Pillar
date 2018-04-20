<?php
namespace SpareParts\Pillar\Mapper\Dibi;


class ColumnInfo
{
	/**
	 * @var string
	 */
	private $columnName;

	/**
	 * @var string
	 */
	private $propertyName;

	/**
	 * @var TableInfo
	 */
	private $tableInfo;

	/**
	 * @var bool
	 */
	private $isPrimaryKey;

	/**
	 * @var bool
	 */
	private $enabledForSelect = true;

	/**
	 * @var string|null
	 */
	private $customSelectSql;

	/**
	 * @var bool
	 */
	private $isDeprecated;

	/**
	 * @param string $columnName
	 * @param string $propertyName
	 * @param TableInfo $tableInfo
	 * @param bool $isPrimaryKey
	 * @param bool $isDeprecated
	 * @param bool $enabledForSelect
	 * @param string|null $customSelectSql
	 */
	public function __construct($columnName, $propertyName, TableInfo $tableInfo, $isPrimaryKey, $isDeprecated, $enabledForSelect, $customSelectSql = null)
	{
		$this->columnName = $columnName;
		$this->propertyName = $propertyName;
		$this->tableInfo = $tableInfo;
		$this->isPrimaryKey = $isPrimaryKey;
		$this->enabledForSelect = $enabledForSelect;
		$this->customSelectSql = $customSelectSql;
		$this->isDeprecated = $isDeprecated;
	}

	/**
	 * @return string
	 */
	public function getColumnName()
	{
		return $this->columnName;
	}

	/**
	 * @return string
	 */
	public function getPropertyName()
	{
		return $this->propertyName;
	}

	/**
	 * @return TableInfo
	 */
	public function getTableInfo()
	{
		return $this->tableInfo;
	}

	/**
	 * @return bool
	 */
	public function isPrimaryKey()
	{
		return $this->isPrimaryKey;
	}

	/**
	 * @return bool
	 */
	public function isDeprecated()
	{
		return $this->isDeprecated;
	}

	/**
	 * @return bool
	 */
	public function isEnabledForSelect()
	{
		return $this->enabledForSelect;
	}

	/**
	 * @return string|null
	 */
	public function getCustomSelectSql()
	{
		return $this->customSelectSql;
	}
}
