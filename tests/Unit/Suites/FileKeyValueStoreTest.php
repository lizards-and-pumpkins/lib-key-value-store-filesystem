<?php

namespace Brera\DataPool\KeyValue\File;

use Brera\Utils\Clearable;

/**
 * @covers  \Brera\DataPool\KeyValue\File\FileKeyValueStore
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
        $this->storageDir = sys_get_temp_dir() . '/brera-lib-key-value-store';

        mkdir($this->storageDir);

        $this->store = new FileKeyValueStore($this->storageDir);
    }

    protected function tearDown()
    {
        array_map('unlink', glob($this->storageDir . '/*'));
        rmdir($this->storageDir);
    }

    public function testItThrowsAnExceptionIfStorageDirIsNotWritable()
    {
        $this->setExpectedException(\Brera\DataPool\KeyValue\KeyValueStoreNotAvailableException::class);
        new FileKeyValueStore('foo');
    }

    public function testItSetsAndGetsAValue()
    {
        $key = 'key';
        $value = 'value';

        $this->store->set($key, $value);
        $this->assertEquals($value, $this->store->get($key));
    }

    public function testItThrowsAnExceptionWhenValueIsNotSet()
    {
        $this->setExpectedException(\Brera\DataPool\KeyValue\KeyNotFoundException::class);
        $this->store->get('not set key');
    }

    public function testItReturnsTrueOnlyAfterValueIsSet()
    {
        $key = 'key';
        $value = 'value';

        $this->assertFalse($this->store->has($key));

        $this->store->set($key, $value);
        $this->assertTrue($this->store->has($key));
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

    public function testItImplementsTheClearableInterface()
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
