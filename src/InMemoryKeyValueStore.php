<?php

namespace LizardsAndPumpkins\DataPool\KeyValue\InMemory;

use LizardsAndPumpkins\DataPool\KeyValue\KeyValueStore;
use LizardsAndPumpkins\DataPool\KeyValue\KeyNotFoundException;

class InMemoryKeyValueStore implements KeyValueStore
{
    /**
     * @var array
     */
    private $store = [];

    /**
     * @param string $key
     * @return mixed
     * @throws KeyNotFoundException
     */
    public function get($key)
    {
        if (!isset($this->store[$key])) {
            throw new KeyNotFoundException(sprintf('Key not found "%s"', $key));
        }
        return $this->store[$key];
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return null
     */
    public function set($key, $value)
    {
        $this->store[$key] = $value;
    }

    /**
     * @param $key
     * @return bool
     */
    public function has($key)
    {
        return array_key_exists($key, $this->store);
    }

    /**
     * @param string[] $keys
     * @return mixed[]
     */
    public function multiGet(array $keys)
    {
        $foundValues = [];

        foreach ($keys as $key) {
            if (array_key_exists($key, $this->store)) {
                $foundValues[$key] = $this->store[$key];
            }
        }

        return $foundValues;
    }

    /**
     * @param array $items
     * @return null
     */
    public function multiSet(array $items)
    {
        $this->store = array_merge($this->store, $items);
    }
}
