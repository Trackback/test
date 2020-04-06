<?php

namespace Core\Application\DataStorage\Source;

use Core\Application\DataStorage\Source\DataStorageSourceInterface as DataStorageSource;
use Core\Application\Source\DatabaseAdapterInterface as DatabaseAdapter;

class DatabaseStorage implements DataStorageSource
{
    /**
     * @var DatabaseAdapter
     */
    private $adapter;
    /**
     * @var string
     */
    private $tableName;

    /**
     * @param DatabaseAdapter $adapter
     * @param string          $tableName
     */
    public function __construct(string $tableName, DatabaseAdapter $adapter)
    {
        $this->adapter   = $adapter;
        $this->tableName = $tableName;
    }

    /**
     * @param array $conditions
     * @return array
     */
    public function findAll(array $conditions): array
    {
        $this->adapter->select($this->tableName, ['*'], $conditions);

        return $this->adapter->getAll();
    }

    /**
     * @param array $conditions
     * @return array
     */
    public function find(array $conditions): array
    {
        $this->adapter->select($this->tableName, ['*'], $conditions);

        return $this->adapter->getOne();
    }

    /**
     * @param array $condition
     * @return bool
     */
    public function delete(array $condition): bool
    {
        return $this->adapter->delete($this->tableName, $condition);
    }

    /**
     * @param array $attributes
     * @return bool
     */
    public function create(array $attributes): bool
    {
        return $this->adapter->create($this->tableName, $attributes);
    }

    /**
     * @param array $attributes
     * @param array $conditions
     * @return bool
     */
    public function update(array $attributes, array $conditions): bool
    {
        return $this->adapter->update($this->tableName, $attributes, $conditions);
    }

    /**
     * @param array $attributesList
     * @return bool
     */
    public function createMultiple(array $attributesList): bool
    {
        $this->adapter->beginTransaction();

        foreach ($attributesList as $attributes) {
            $this->adapter->create($this->tableName, $attributes);
        }

        return $this->adapter->commit();
    }
}
