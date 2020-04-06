<?php

namespace Core\Application\DataStorage\Source;

interface DataStorageSourceInterface
{
    /**
     * @param array $conditions
     * @return array
     */
    public function find(array $conditions): array;
    
    /**
     * @param array $conditions
     * @return array
     */
    public function findAll(array $conditions): array;
    
    /**
     * @param array $condition
     * @return bool
     */
    public function delete(array $condition): bool;
    
    /**
     * @param array $attributes
     * @return bool
     */
    public function create(array $attributes): bool;
    
    /**
     * @param array $attributes
     * @param array $conditions
     * @return bool
     */
    public function update(array $attributes, array $conditions): bool;
    
    /**
     * @param array $attributes
     * @return bool
     */
    public function createMultiple(array $attributes): bool;
}
