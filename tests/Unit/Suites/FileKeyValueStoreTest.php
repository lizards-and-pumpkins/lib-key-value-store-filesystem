<?php

declare(strict_types=1);

namespace LizardsAndPumpkins\DataPool\KeyValueStore\File;

use LizardsAndPumpkins\DataPool\KeyValueStore\Exception\KeyNotFoundException;
use LizardsAndPumpkins\DataPool\KeyValueStore\Exception\KeyValueStoreNotAvailableException;
use LizardsAndPumpkins\DataPool\KeyValueStore\File\Exception\SnippetCanNotBeStoredException;
use LizardsAndPumpkins\Util\Storage\Clearable;

/**
 * @covers \LizardsAndPumpkins\DataPool\KeyValueStore\File\FileKeyValueStore
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

    /**
     * @var bool
     */
    private static $diskIsFull;

    public static function isDiskFull() : bool
    {
        return self::$diskIsFull;
    }

    protected function setUp()
    {
        self::$diskIsFull = false;

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
        $this->expectException(KeyValueStoreNotAvailableException::class);
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
        $this->expectException(KeyNotFoundException::class);
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
        $result = $this->store->multiGet(...$keys);

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

    public function testKeyCanContainSpecialCharacters()
    {
        $key = 'foo/bar?baz=qux';
        $value = 'value';

        $this->store->set($key, $value);

        $this->assertSame($value, $this->store->get($key));
    }

    public function testExceptionIsThrownIfSnippetCouldNotBeWritten()
    {
        self::$diskIsFull = true;
        $this->expectException(SnippetCanNotBeStoredException::class);
        $this->store->set('foo', 'bar');
    }
}

/**
 * @param string $filename
 * @param mixed $data
 * @param int|null $flags
 * @param resource|null $context
 * @return int|bool
 */
function file_put_contents(string $filename, $data, int $flags = null, $context = null)
{
    if (FileKeyValueStoreTest::isDiskFull()) {
        return false;
    }

    return \file_put_contents($filename, $data, $flags, $context);
}
