<?php
namespace SpareParts\Pillar\Mapper\Dibi;

class TableInfo
{
	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var string
	 */
	private $identifier;

	/**
	 * @var string|null
	 */
	private $sqlJoinCode;

	/**
	 * TableInfo constructor.
	 *
	 * @param string $name
	 * @param string $identifier
	 * @param string|null $sqlJoinCode
	 */
	public function __construct($name, $identifier, $sqlJoinCode = null)
	{
		$this->name = $name;
		$this->identifier = $identifier;
		$this->sqlJoinCode = $sqlJoinCode;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getIdentifier()
	{
		return $this->identifier;
	}

	/**
	 * @return null|string
	 */
	public function getSqlJoinCode()
	{
		return $this->sqlJoinCode;
	}
}
