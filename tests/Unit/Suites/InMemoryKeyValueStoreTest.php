<?php

namespace LizardsAndPumpkins\DataPool\KeyValue\InMemory;

use LizardsAndPumpkins\DataPool\KeyValue\KeyNotFoundException;
use LizardsAndPumpkins\Utils\Clearable;

/**
 * @covers \LizardsAndPumpkins\DataPool\KeyValue\InMemory\InMemoryKeyValueStore
 */
class InMemoryKeyValueStoreTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var InMemoryKeyValueStore
     */
    private $store;

    public function setUp()
    {
        $this->store = new InMemoryKeyValueStore;
    }

    public function testValueIsSetAndRetrieved()
    {
        $key = 'key';
        $value = 'value';

        $this->store->set($key, $value);
        $this->assertEquals($value, $this->store->get($key));
    }

    public function testTrueIsReturnedOnlyAfterValueIsSet()
    {
        $key = 'key';
        $value = 'value';

        $this->assertFalse($this->store->has($key));

        $this->store->set($key, $value);
        $this->assertTrue($this->store->has($key));
    }

    public function testExceptionIsThrownIfValueIsNotSet()
    {
        $this->setExpectedException(KeyNotFoundException::class);
        $this->store->get('not set key');
    }

    public function testMultipleKeysAreSetAndRetrieved()
    {
        $keys = ['key1', 'key2'];
        $values = ['foo', 'bar'];
        $items = array_combine($keys, $values);

        $this->store->multiSet($items);
        $result = $this->store->multiGet($keys);

        $this->assertSame($items, $result);
    }

    public function testClearableInterfaceIsImplemented()
    {
        $this->assertInstanceOf(Clearable::class, $this->store);
    }

    public function testStorageContentIsFlushed()
    {
        $key = 'key';
        $value = 'value';

        $this->store->set($key, $value);
        $this->store->clear();
        $this->assertFalse($this->store->has($key));
    }
}
