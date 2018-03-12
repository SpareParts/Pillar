<?php
namespace SpareParts\Pillar\Mapper\Dibi;

use SpareParts\Pillar\Mapper\Dibi\ColumnInfo;
use SpareParts\Pillar\Mapper\Dibi\TableInfo;

interface IEntityMapping
{
	/**
	 * @return string
	 */
	public function getEntityClassName();

	/**
	 * @return TableInfo[]
	 */
	public function getTables();

	/**
	 * @param string $tableIdentifier
	 * @return ColumnInfo[]
	 */
	public function getColumnsForTable($tableIdentifier);

	/**
	 * @return bool
	 */
	public function isVirtualEntity();
}
