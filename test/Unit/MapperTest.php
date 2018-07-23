<?php
//namespace SpareParts\Pillar\Test\Unit;
//
//use Doctrine\Common\Annotations\AnnotationReader;
//use Doctrine\Common\Annotations\AnnotationRegistry;
//use PHPUnit\Framework\TestCase;
//use SpareParts\Pillar\Assistant\Dibi\DibiEntityAssistant;
//use SpareParts\Pillar\Mapper\Annotation\Storage;
//use SpareParts\Pillar\Mapper\Dibi\AnnotationMapper;
//use SpareParts\Pillar\Test\Fixtures\GridProduct;

//class MapperTest extends TestCase
//{
//
//
//	/**
//	 * @test
//	 */
//	public function test()
//	{
//		AnnotationRegistry::registerLoader("class_exists");
//
//		$reader = new AnnotationReader();
//		$mapper = new AnnotationMapper($reader);
//
//		$connection = new \Dibi\Connection([
//			'username' => 'ulozto_rw',
//			'password' => 'D6b6WJUf7W',
//			'host' => 'mysqlmaster.ulozto',
//			'database' => 'ulozto',
//		]);

//		$entityMapping = $mapper->getEntityMapping(GridProduct::class);


//		$assistant = new DibiEntityAssistant($mapper, $connection);

//		$fluent = $assistant->fluent(GridProduct::class)
//			->selectEntityProperties()
//			->fromEntityDataSources()
//			->where('`products`.`name` LIKE %s', '%trubka%');
//
//		$a = (string) $fluent;
//
//		$fluent->fetchAll();

//		$product = new GridProduct([
//			'id' => 1,
//			'name' => 'rofl',
//		]);
//		$product->setName('lol');
//
//		$assistant->update($product, ['products']);
//	}
//}
