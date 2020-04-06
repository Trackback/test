<?php

namespace Core\Application\Source;


interface DatabaseAdapterInterface
{
    /**
     * @return array
     */
    public function getAll(): array;
    
    /**
     * @return array
     */
    public function getOne(): array;
    
    /**
     * @param string $table
     * @param array  $fields
     * @param array  $conditions
     */
    public function select(string $table, array $fields, array $conditions): void;
    
    /**
     * @return int
     */
    public function rowCount(): int;
    
    /**
     * @param string $table
     * @param array  $attributes
     * @return bool
     */
    public function create(string $table, array $attributes): bool;
    
    /**
     * @param string $table
     * @param array  $changes
     * @param array  $conditions
     * @return mixed
     */
    public function update(string $table, array $changes, array $conditions);
    
    /**
     * @param string $table
     * @param array  $conditions
     * @return bool
     */
    public function delete(string $table, array $conditions): bool;
    
    /**
     * @return int
     */
    public function lastInsertId(): int;
    
    /**
     * @return bool
     */
    public function beginTransaction(): bool;
    
    /**
     * @return bool
     */
    public function commit(): bool;
    
    /**
     * @return bool
     */
    public function rollBack(): bool;
}
