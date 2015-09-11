<?php

namespace Brera\DataPool\KeyValue\InMemory;

use Brera\Utils\Clearable;

/**
 * @covers  \Brera\DataPool\KeyValue\InMemory\InMemoryKeyValueStore
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

    public function testItSetsAndGetsAValue()
    {
        $key = 'key';
        $value = 'value';

        $this->store->set($key, $value);
        $this->assertEquals($value, $this->store->get($key));
    }

    public function testItReturnsTrueOnlyAfterValueIsSet()
    {
        $key = 'key';
        $value = 'value';

        $this->assertFalse($this->store->has($key));

        $this->store->set($key, $value);
        $this->assertTrue($this->store->has($key));
    }

    public function testItThrowsAnExceptionWhenValueIsNotSet()
    {
        $this->setExpectedException(\Brera\DataPool\KeyValue\KeyNotFoundException::class);
        $this->store->get('not set key');
    }

    public function testItSetsAndGetsMultipleKeys()
    {
        $keys = ['key1', 'key2'];
        $values = ['foo', 'bar'];
        $items = array_combine($keys, $values);

        $this->store->multiSet($items);
        $result = $this->store->multiGet($keys);

        $this->assertSame($items, $result);
    }

    public function testItIsClearable()
    {
        $this->assertInstanceOf(Clearable::class, $this->store);
    }

    public function testItFlushesTheContents()
    {
        $key = 'key';
        $value = 'value';

        $this->store->set($key, $value);
        $this->store->clear();
        $this->assertFalse($this->store->has($key));
    }
}
