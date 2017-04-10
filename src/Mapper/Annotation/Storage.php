<?php
namespace SpareParts\Pillar\Mapper\Annotation;

use Doctrine\Common\Annotations\Annotation\Required;

/**
 * @Annotation
 */
class Storage
{
	/**
	 * @var string
	 * @Required()
	 */
	protected $type;

	public function __construct($values)
	{
		if (isset($values['value'])) {
			$this->type = $values['value'];
		}
		if (isset($values['type'])) {
			$this->type = $values['type'];
		}
	}

	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}
}
