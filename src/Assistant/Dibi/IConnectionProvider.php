<?php
namespace SpareParts\Pillar\Assistant\Dibi;


interface IConnectionProvider
{
	/**
	 * @return \Dibi\Connection
	 */
	public function getConnection();
}
