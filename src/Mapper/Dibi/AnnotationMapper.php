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

				$tableInfoList[$classAnnotation->getIdentifier()] = new TableInfo(
					$classAnnotation->getName(),
					$classAnnotation->getIdentifier() ?: $classAnnotation->getName(),
					$classAnnotation->getCode()
				);
			}

			$columnInfoList = [];
			foreach ($class->getProperties() as $property) {
				$enabledForSelect = true;
				foreach ($this->annotationReader->getPropertyAnnotations($property) as $propertyAnnotation) {
					if (!($propertyAnnotation instanceof Column)) {
						continue;
					}

					if (!isset($tableInfoList[$propertyAnnotation->getTable()])) {
						throw new EntityMappingException(sprintf('Entity :`%s` property: `%s` is mapped to table identified as: `%s`, but no such table identifier is present.', $className, $property->getName(), $propertyAnnotation->getTable()));
					}

					$columnInfoList[] = new ColumnInfo(
						$propertyAnnotation->getName() ?: $property->getName(),
						$property->getName(),
						$tableInfoList[$propertyAnnotation->getTable()],
						$propertyAnnotation->isPrimary(),
						$enabledForSelect
					);
					// only first @column annotation should be used for selecting
					// all following @column are there for saving/updating
					$enabledForSelect = false;
				}
			}

			$this->dibiMappingCache[$className] = new EntityMapping(
				$className, $tableInfoList, $columnInfoList
			);
		}
		return $this->dibiMappingCache[$className];
	}
}
