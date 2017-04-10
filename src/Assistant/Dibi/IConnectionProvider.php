<?php
namespace SpareParts\Pillar\Assistant\Dibi;


interface IConnectionProvider
{
	/**
	 * @return \DibiConnection
	 */
	public function getConnection();
}
