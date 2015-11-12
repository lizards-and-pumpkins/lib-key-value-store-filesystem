<?php

namespace LizardsAndPumpkins\DataPool\KeyValue\File;

use LizardsAndPumpkins\DataPool\KeyValue\Exception\KeyNotFoundException;
use LizardsAndPumpkins\DataPool\KeyValue\Exception\KeyValueStoreNotAvailableException;
use LizardsAndPumpkins\Utils\Clearable;

/**
 * @covers \LizardsAndPumpkins\DataPool\KeyValue\File\FileKeyValueStore
 */
class FileKeyValueStoreTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FileKeyValueStore
     */
    private $store;

    /**
     * @var string
     */
    private $storageDir;

    protected function setUp()
    {
        $this->storageDir = sys_get_temp_dir() . '/lizards-and-pumpkins-lib-key-value-store';

        mkdir($this->storageDir);

        $this->store = new FileKeyValueStore($this->storageDir);
    }

    protected function tearDown()
    {
        array_map('unlink', glob($this->storageDir . '/*'));
        rmdir($this->storageDir);
    }

    public function testExceptionIsThrownIfStorageDirIsNotWritable()
    {
        $this->setExpectedException(KeyValueStoreNotAvailableException::class);
        new FileKeyValueStore('foo');
    }

    public function testValueIsSetAndRetrieved()
    {
        $key = 'key';
        $value = 'value';

        $this->store->set($key, $value);
        $this->assertEquals($value, $this->store->get($key));
    }

    public function testExceptionIsThrownIfValueIsNotSet()
    {
        $this->setExpectedException(KeyNotFoundException::class);
        $this->store->get('not set key');
    }

    public function testTrueIsReturnedOnlyAfterValueIsSet()
    {
        $key = 'key';
        $value = 'value';

        $this->assertFalse($this->store->has($key));

        $this->store->set($key, $value);
        $this->assertTrue($this->store->has($key));
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

    public function testStorageContentsIsFlushed()
    {
        $key = 'key';
        $value = 'value';
        
        $this->store->set($key, $value);
        $this->store->clear();
        $this->assertFalse($this->store->has($key));
    }
}
