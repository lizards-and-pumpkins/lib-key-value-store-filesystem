<?php

namespace LizardsAndPumpkins\DataPool\KeyValueStore\File;

use LizardsAndPumpkins\DataPool\KeyValueStore\Exception\KeyNotFoundException;
use LizardsAndPumpkins\DataPool\KeyValueStore\Exception\KeyValueStoreNotAvailableException;
use LizardsAndPumpkins\DataPool\KeyValueStore\KeyValueStore;
use LizardsAndPumpkins\Util\FileSystem\LocalFilesystem;
use LizardsAndPumpkins\Util\Storage\Clearable;

class FileKeyValueStore implements KeyValueStore, Clearable
{
    /**
     * @var string
     */
    private $storagePath;

    public function __construct(string $storagePath)
    {
        if (!is_writable($storagePath)) {
            throw new KeyValueStoreNotAvailableException(sprintf(
                'Directory "%s" is not writable by the filesystem key-value storage',
                $storagePath
            ));
        }

        $this->storagePath = $storagePath;
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value)
    {
        file_put_contents($this->getFilePathByKey($key), $value, LOCK_EX);
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function get($key)
    {
        if (!$this->has($key)) {
            throw new KeyNotFoundException(sprintf('Key not found "%s"', $key));
        }

        return file_get_contents($this->getFilePathByKey($key));
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has($key) : bool
    {
        return is_readable($this->getFilePathByKey($key));
    }

    /**
     * @param string[] $keys
     * @return mixed[]
     */
    public function multiGet(array $keys) : array 
    {
        return array_reduce($keys, function(array $carry, $key) {
            if (!$this->has($key)) {
                return $carry;
            }

            return array_merge($carry, [$key => $this->get($key)]);
        }, []);
    }

    /**
     * @param string[] $items
     */
    public function multiSet(array $items)
    {
        foreach ($items as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * @param string $key
     * @return string
     */
    private function getFilePathByKey($key) : string 
    {
        return $this->storagePath . '/' . urlencode($key);
    }

    public function clear()
    {
        (new LocalFilesystem)->removeDirectoryContents($this->storagePath);
    }
}
