<?php

namespace Core\Application\DataStorage;

use Core\Application\DataStorage\DataStorageInterface;
use Core\Application\DataStorage\Source\DataStorageSourceInterface as DataStorageSource;

class UserDataStorage implements DataStorageInterface
{
    /**
     * @var DataStorageSource $source
     */
    private $source;
    
    /**
     * UserDataStorage constructor.
     * @param DataStorageSource $source
     */
    public function __construct(DataStorageSource $source)
    {
        $this->source = $source;
    }
    
    /**
     * @param int $id
     * @return array
     */
    public function findById(int $id): array
    {
        $condition = ['id' => $id];
        
        return $this->find($condition);
    }
    
    /**
     * @param array $conditions
     * @return array
     */
    public function find(array $conditions): array
    {
        return $this->source->find($conditions);
    }
    
    /**
     * @param array $condition
     * @return bool
     */
    public function delete(array $condition): bool
    {
        return $this->source->delete($condition);
    }
    
    /**
     * @param array $attributes
     * @return bool
     */
    public function create(array $attributes): bool
    {
        return $this->source->create($attributes);
    }
    
    /**
     * @param array $attributes
     * @param array $conditions
     * @return bool
     */
    public function update(array $attributes, array $conditions): bool
    {
        return $this->source->update($attributes, $conditions);
    }
    
    /**
     * @param array $attributes
     * @return bool
     */
    public function createMultiple(array $attributes): bool
    {
        return $this->source->createMultiple($attributes);
    }
}
