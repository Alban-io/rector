<?php
declare(strict_types = 1);

namespace TBolier\RethinkQL\UnitTest\Serializer;

use Mockery;
use Symfony\Component\Serializer\Serializer;
use TBolier\RethinkQL\Query\Options;
use TBolier\RethinkQL\Serializer\QueryNormalizer;
use TBolier\RethinkQL\UnitTest\BaseUnitTestCase;

class QueryNormalizerTest extends BaseUnitTestCase
{
    /**
     * @var QueryNormalizer
     */
    private $normalizer;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->normalizer = new QueryNormalizer();

        $serializer = new Serializer([$this->normalizer]);

        $this->normalizer->setSerializer($serializer);
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testNormalizeWithStdClass(): void
    {
        $object = new \stdClass();
        $object->foo = 'bar';

        $data = $this->normalizer->normalize($object);

        $this->assertEquals(['foo' => 'bar'], $data);
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testNormalizeWithOptions(): void
    {
        $object = new Options();
        $object->setDb('foobar');

        $expectedObject = new \stdClass();
        $expectedObject->db = [0 => 14, 1 => ['foobar']];

        $data = $this->normalizer->normalize($object);

        $this->assertEquals($expectedObject, $data);
    }

// @todo: why the exception is no longer triggered

//    /**
//     * @expectedException \Symfony\Component\Serializer\Exception\CircularReferenceException
//     * @expectedExceptionMessage A circular reference has been detected when serializing the object of class "stdClass"
//     * (configured limit: 1)
//     * @return void
//     */
//    public function testNormalizeWithCircularReference(): void
//    {
//        $object = new \stdClass();
//        $object->foo = 'bar';
//
//        $context = [
//            'circular_reference_limit' => [
//                spl_object_hash($object) => 1,
//            ],
//        ];
//
//        $this->normalizer->normalize($object, null, $context);
//    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testNormalizeWithJsonSerializable(): void
    {
        $expectedReturn = ['foo' => 'bar'];

        $object = Mockery::mock('\JsonSerializable');
        $object->shouldReceive('jsonSerialize')->once()->andReturn($expectedReturn);


        $data = $this->normalizer->normalize($object);

        $this->assertEquals($expectedReturn, $data);
    }

    /**
     * @return void
     */
    public function testInvalidArgumentExceptionThrownOnInvalidClass(): void
    {
        $this->expectException('Symfony\Component\Serializer\Exception\InvalidArgumentException');
        $this->expectExceptionMessage('The ArrayObject must implement "JsonSerializable"');
        $object = new \ArrayObject();

        $this->normalizer->normalize($object);
    }

    /**
     * @return void
     */
    public function testLogicExceptionThrownOnInvalidNormalizer(): void
    {
        $this->expectException('Symfony\Component\Serializer\Exception\LogicException');
        $this->expectExceptionMessage('Cannot normalize object because injected serializer is not a normalizer');
        $object = new \stdClass();
        $object->foo = 'bar';

        $serializerMock = Mockery::mock('\Symfony\Component\Serializer\SerializerInterface');
        $this->normalizer->setSerializer($serializerMock);

        $this->normalizer->normalize($object);
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testSupportsDenormalizationReturnsFalse(): void
    {
        $this->assertFalse($this->normalizer->supportsDenormalization('foo', 'foo', 'foo'));
    }

    /**
     * @return void
     */
    public function testIfDenormalizeThrowsLogicException(): void
    {
        $this->expectException('Symfony\Component\Serializer\Exception\LogicException');
        $this->expectExceptionMessage('Cannot denormalize with "TBolier\RethinkQL\Serializer\QueryNormalizer".');
        $this->normalizer->denormalize('foo', 'bar');
    }
}
