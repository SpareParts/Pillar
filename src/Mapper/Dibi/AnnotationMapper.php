<?php
namespace SpareParts\Pillar\Mapper\Dibi;

use Doctrine\Common\Annotations\Reader;
use SpareParts\Pillar\Entity\IEntity;
use SpareParts\Pillar\Mapper\Annotation\Column;
use SpareParts\Pillar\Mapper\Annotation\Table;
use SpareParts\Pillar\Mapper\EntityMappingException;
use SpareParts\Pillar\Mapper\IMapper;

class AnnotationMapper implements IMapper
{
	/**
	 * @var Reader
	 */
	private $annotationReader;

	/**
	 * @var IEntityMapping[]
	 */
	private $dibiMappingCache = [];

	/**
	 * Mapper constructor.
	 *
	 * @param Reader $annotationReader
	 */
	public function __construct(Reader $annotationReader)
	{
		$this->annotationReader = $annotationReader;
	}

	/**
	 * @param string|IEntity $classnameOrInstance
	 * @return IEntityMapping
	 * @throws EntityMappingException
	 */
	public function getEntityMapping($classnameOrInstance)
	{
		$className = $classnameOrInstance;
		if (is_object($classnameOrInstance)) {
			if (!($classnameOrInstance instanceof IEntity)) {
				throw new EntityMappingException(sprintf('Expected class implementing IEntity interface, got %s instead', get_class($classnameOrInstance)));
			}
			$className = get_class($classnameOrInstance);
		}

		if (!isset($this->dibiMappingCache[$className])) {
			$class = new \ReflectionClass($className);
			$tableInfoList = [];

			foreach ($this->annotationReader->getClassAnnotations($class) as $classAnnotation) {
				if (!($classAnnotation instanceof Table)) {
					continue;
				}

				$identifier = $classAnnotation->getIdentifier() ?: $classAnnotation->getName();
				$tableInfoList[$identifier] = new TableInfo(
					$classAnnotation->getName(),
					$identifier,
					$classAnnotation->getCode()
				);
			}

			$columnInfoList = [];
			foreach ($class->getProperties() as $property) {
				$enabledForSelect = true;
				// null means this property is not mapped to ANY table = probably not mapped column - ignore it.
				// true means this property is mapped to a table, and that table was not used - exception
				// false means this property is mapped to a table and at least one of those tables is used - ok
				$danglingProperty = null;
				foreach ($this->annotationReader->getPropertyAnnotations($property) as $propertyAnnotation) {
					if (!($propertyAnnotation instanceof Column)) {
						continue;
					}
					if (is_null($danglingProperty)) {
						$danglingProperty = true;
					}

					if (!isset($tableInfoList[$propertyAnnotation->getTable()])) {
						// this is possibly not a mistake - property may have multiple Column annotations, and not be using all at once in the current entity
						continue;
//						throw new EntityMappingException(sprintf('Entity :`%s` property: `%s` is mapped to table identified as: `%s`, but no such table identifier is present.', $className, $property->getName(), $propertyAnnotation->getTable()));
					}
					$danglingProperty = false;

					$columnInfoList[] = new ColumnInfo(
						$propertyAnnotation->getName() ?: $property->getName(),
						$property->getName(),
						$tableInfoList[$propertyAnnotation->getTable()],
						$propertyAnnotation->isPrimary(),
						$propertyAnnotation->isDeprecated(),
						$enabledForSelect,
						$propertyAnnotation->getCustomSelect()
					);
					// only first @column annotation should be used for selecting
					// all following @column are there for saving/updating
					$enabledForSelect = false;
				}

				if ($danglingProperty === true) {
					throw new EntityMappingException(sprintf('Entity: `%s` has property `%s` mapped to tables, but none of those tables are used in the entity. Maybe you forgot to use the table in the select?', $className, $property->getName()));
				}
			}

			$this->dibiMappingCache[$className] = new EntityMapping(
				$className, $tableInfoList, $columnInfoList
			);
		}
		return $this->dibiMappingCache[$className];
	}
}
