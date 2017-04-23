<?php
namespace SpareParts\Pillar\Assistant\Dibi\Sorting;

use SpareParts\Enum\Enum;

/**
 * @method static ASCENDING()
 * @method static DESCENDING()
 */
class SortingDirectionEnum extends Enum
{
	protected static $values = [
		'ASCENDING',
		'DESCENDING',
	];
}
