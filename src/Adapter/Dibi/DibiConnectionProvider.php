<?php
namespace SpareParts\Pillar\Adapter\Dibi;

use SpareParts\Pillar\Assistant\Dibi\IConnectionProvider;

class DibiConnectionProvider implements IConnectionProvider
{
	/**
	 * @var \DibiConnection
	 */
	private $connection;

	public function __construct(\DibiConnection $connection)
	{
		$this->connection = $connection;
	}

	public function getConnection()
	{
		return $this->connection;
	}
}
