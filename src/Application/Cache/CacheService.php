<?php

namespace Core\Application\Cache;

use Core\Application\Cache\Source\CacheSourceInterface as CacheSource;
use Core\Application\DataStorage\Source\DataStorageSourceInterface as DataStorageSource;

class CacheService implements DataStorageSource
{
    /**
     * @var CacheSource $source ;
     */
    private $source;
    /**
     * @var DataStorageSource
     */
    private $dataStorageSource;
    /**
     * @var string
     */
    private $keyPrefix;
    
    /**
     * @param CacheSource       $source
     * @param DataStorageSource $dataStorageSource
     */
    public function __construct(CacheSource $source, DataStorageSource $dataStorageSource)
    {
        $this->source            = $source;
        $this->dataStorageSource = $dataStorageSource;
        $this->keyPrefix         = get_class($dataStorageSource) . "_";
    }
    
    /**
     * @param array $conditions
     * @return array
     */
    public function find(array $conditions): array
    {
        $key = $this->getKey($conditions);
        
        if ($this->source->present($key)) {
            return $this->source->get($key);
        }
        
        $dataFromStorage = $this->dataStorageSource->find($conditions);
        $this->source->put($key, $dataFromStorage);
        
        return $dataFromStorage;
    }
    
    /**
     * @param array $conditions
     * @return array
     */
    public function findAll(array $conditions): array
    {
        $key = $this->getKey($conditions);
        
        if ($this->source->present($key)) {
            return $this->source->get($key);
        }
        
        $dataFromStorage = $this->dataStorageSource->findAll($conditions);
        $this->source->put($key, $dataFromStorage);
        
        return $dataFromStorage;
    }
    
    /**
     * @param array $condition
     * @return bool
     */
    public function delete(array $condition): bool
    {
        $key = $this->getKey($condition);
        
        $this->source->delete($key);
        
        return $this->dataStorageSource->delete($condition);
    }
    
    /**
     * @param array $attributes
     * @return bool
     */
    public function create(array $attributes): bool
    {
        return $this->dataStorageSource->create($attributes);
    }
    
    /**
     * @param array $attributes
     * @param array $conditions
     * @return bool
     */
    public function update(array $attributes, array $conditions): bool
    {
        $updateResult = $this->dataStorageSource->update($attributes, $conditions);
        $key          = $this->getKey($conditions);
        
        if ($this->source->present($key)) {
            $data = $this->dataStorageSource->find($conditions);
            
            $this->source->update($key, $data);
        }
        
        return $updateResult;
    }
    
    /**
     * @param array $attributes
     * @return bool
     */
    public function createMultiple(array $attributes): bool
    {
        return $this->dataStorageSource->createMultiple($attributes);
    }
    
    /**
     * @param array $conditions
     * @return string
     */
    private function getKey(array $conditions): string
    {
        $keyString = implode($conditions);
        
        return $this->keyPrefix . md5($keyString);
    }
}
