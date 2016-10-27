<?php

declare(strict_types=1);

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
    public function set(string $key, $value)
    {
        file_put_contents($this->getFilePathByKey($key), $value, LOCK_EX);
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function get(string $key)
    {
        if (!$this->has($key)) {
            throw new KeyNotFoundException(sprintf('Key not found "%s"', $key));
        }

        return file_get_contents($this->getFilePathByKey($key));
    }

    public function has(string $key) : bool
    {
        return is_readable($this->getFilePathByKey($key));
    }

    /**
     * @param string[] $keys
     * @return mixed[]
     */
    public function multiGet(string ...$keys) : array
    {
        return array_reduce($keys, function (array $carry, $key) {
            if (!$this->has($key)) {
                return $carry;
            }

            return array_merge($carry, [$key => $this->get($key)]);
        }, []);
    }

    /**
     * @param mixed[] $items
     */
    public function multiSet(array $items)
    {
        foreach ($items as $key => $value) {
            $this->set($key, $value);
        }
    }

    private function getFilePathByKey(string $key) : string
    {
        return $this->storagePath . '/' . urlencode($key);
    }

    public function clear()
    {
        (new LocalFilesystem)->removeDirectoryContents($this->storagePath);
    }
}
