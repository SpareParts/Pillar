<?php
namespace SpareParts\Pillar\Mapper\Annotation;

use Doctrine\Common\Annotations\Annotation\Required;

/**
 * @Annotation
 * @Target("CLASS")
 */
class Table
{
	/**
	 * @var string
	 * @Required()
	 */
	protected $name;

	/**
	 * @var string
	 */
	protected $identifier;

	/**
	 * @var string
	 */
	protected $code;

	public function __construct($values)
	{
		if (isset($values['value'])) {
			$this->name = $values['value'];
		}
		if (isset($values['name'])) {
			$this->name = $values['name'];
		}
		$this->identifier = $this->name;
		if (isset($values['identifier'])) {
			$this->identifier = $values['identifier'];
		}
		if (isset($values['code'])) {
			$this->code = $values['code'];
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
	public function getIdentifier()
	{
		return $this->identifier;
	}

	/**
	 * @return string
	 */
	public function getCode()
	{
		return $this->code;
	}
}
