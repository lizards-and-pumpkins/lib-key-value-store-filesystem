<?php

namespace Brera\KeyValue\InMemory;

/**
 * @covers  \Brera\KeyValue\File\InMemoryKeyValueStore
 */
class InMemoryKeyValueStoreTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var InMemoryKeyValueStore
     */
    private $store;

    public function setUp()
    {
        $this->store = new InMemoryKeyValueStore();
    }

    /**
     * @test
     */
    public function itShouldSetAndGetAValue()
    {
        $key = 'key';
        $value = 'value';

        $this->store->set($key, $value);
        $this->assertEquals($value, $this->store->get($key));
    }

    /**
     * @test
     */
    public function itShouldReturnTrueOnlyAfterValueIsSet()
    {
        $key = 'key';
        $value = 'value';

        $this->assertFalse($this->store->has($key));

        $this->store->set($key, $value);
        $this->assertTrue($this->store->has($key));
    }

    /**
     * @test
     * @expectedException \Brera\KeyValue\KeyNotFoundException
     */
    public function itShouldThrowAnExceptionWhenValueIsNotSet()
    {
        $this->store->get('not set key');
    }

	/**
	 * @test
	 */
	public function itShouldSetAndGetMultipleKeys()
	{
		$keys = ['key1', 'key2'];
		$values = ['foo', 'bar'];
		$items = array_combine($keys, $values);

		$this->store->multiSet($items);
		$result = $this->store->multiGet($keys);

		$this->assertSame($items, $result);
	}
}
