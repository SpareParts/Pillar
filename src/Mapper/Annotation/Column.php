<?php
namespace SpareParts\Pillar\Mapper\Annotation;

use Doctrine\Common\Annotations\Annotation\Required;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
class Column
{
	/**
	 * @var string
	 * @Required()
	 */
	protected $name;

	/**
	 * @var string
	 * @Required()
	 */
	protected $table;

	/**
	 * @var string|null
	 */
	protected $customSelect;

	/**
	 * @var bool
	 */
	protected $primary = false;

	public function __construct($values)
	{
		if (isset($values['value'])) {
			$this->name = $values['value'];
		}
		if (isset($values['name'])) {
			$this->name = $values['name'];
		}
		if (isset($values['table'])) {
			$this->table = $values['table'];
		}
		if (isset($values['primary'])) {
			$this->primary = $values['primary'];
		}
		if (isset($values['customSelect'])) {
			$this->customSelect = $values['customSelect'];
		}
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
	public function getTable()
	{
		return $this->table;
	}

	/**
	 * @return bool
	 */
	public function isPrimary()
	{
		return $this->primary;
	}

	/**
	 * @return string|null
	 */
	public function getCustomSelect()
	{
		return $this->customSelect;
	}
}
