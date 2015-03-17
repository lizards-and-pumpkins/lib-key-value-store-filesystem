<?php

namespace Brera\DataPool\KeyValue\File;

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

    /**
     * @test
     * @expectedException \Brera\DataPool\KeyValue\KeyValueStoreNotAvailableException
     */
    public function itShouldThrowAnExceptionIfStorageDirIsNotWritable()
    {
        new FileKeyValueStore('foo');
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
     * @expectedException \Brera\DataPool\KeyValue\KeyNotFoundException
     */
    public function itShouldThrowAnExceptionWhenValueIsNotSet()
    {
        $this->store->get('not set key');
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
