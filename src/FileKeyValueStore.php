<?php

namespace Brera\DataPool\KeyValue\File;

use Brera\DataPool\KeyValue\KeyValueStore;
use Brera\DataPool\KeyValue\KeyNotFoundException;
use Brera\DataPool\KeyValue\KeyValueStoreNotAvailableException;

class FileKeyValueStore implements KeyValueStore
{
    /**
     * @var string
     */
    private $storagePath;

    /**
     * @var string
     */
    private $keyPrefix;

    public function __construct($storagePath = null, $keyFilePrefix = 'key_')
    {
        if (is_null($storagePath)) {
            $storagePath = sys_get_temp_dir();
        }

        if (!is_writable($storagePath)) {
            throw new KeyValueStoreNotAvailableException(sprintf(
                'Directory "%s" is not writable by the filesystem key-value storage',
                $storagePath
            ));
        }

        $this->storagePath = $storagePath;
        $this->keyPrefix = $keyFilePrefix;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return null
     */
    public function set($key, $value)
    {
        file_put_contents($this->getFilePathByKey($key), $value, LOCK_EX);
    }

    /**
     * @param string $key
     * @return mixed
     * @throws KeyNotFoundException
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
    public function has($key)
    {
        return is_readable($this->getFilePathByKey($key));
    }

    /**
     * @param array $keys
     * @return array
     */
    public function multiGet(array $keys)
    {
        $items = [];

        foreach ($keys as $key) {
            if ($this->has($key)) {
                $items[$key] = $this->get($key);
            }
        }

        return $items;
    }

    /**
     * @param array $items
     * @return null
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
    private function getFilePathByKey($key)
    {
        return $this->storagePath . DIRECTORY_SEPARATOR . $this->keyPrefix . $key;
    }
}
