<?php
namespace SpareParts\Pillar\Adapter\Dibi;

use SpareParts\Pillar\Assistant\Dibi\IConnectionProvider;

class DibiConnectionProvider implements IConnectionProvider
{
	/**
	 * @var \Dibi\Connection
	 */
	private $connection;

	public function __construct(\Dibi\Connection $connection)
	{
		$this->connection = $connection;
	}

	public function getConnection()
	{
		return $this->connection;
	}
}
