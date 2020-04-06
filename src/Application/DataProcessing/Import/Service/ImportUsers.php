<?php

namespace Core\Application\DataProcessing;

use Core\Application\DataProcessing\Import\Source\ImportSourceInterface as ImportSource;
use Core\Application\DataStorage\DataStorageInterface as DataStorage;
use InvalidArgumentException;
use LengthException;
use RuntimeException;
use UnexpectedValueException;

class ImportUsers
{
    /**
     * @var ImportSource $source
     */
    private $source;
    
    /**
     * @var DataStorage $userDataStorage
     */
    private $userDataStorage;
    
    /**
     * @param ImportSource $source
     * @param DataStorage  $userDataStorage
     */
    public function __construct(ImportSource $source, DataStorage $userDataStorage)
    {
        ini_set('max_execution_time', 300);
        set_time_limit(300);
    
        $this->source          = $source;
        $this->userDataStorage = $userDataStorage;
    }
    
    /**
     * @param array $fieldsSet
     * @param int   $chunkSize
     * @param bool  $skipHeader
     */
    public function import(array $fieldsSet, int $chunkSize = 100, bool $skipHeader = false)
    {
        if ($this->source->isEmpty()) {
            throw new LengthException("Data not found in input source");
        }
        
        if ($fieldsSet == []) {
            throw new InvalidArgumentException("Empty fieldset");
        }
        
        if ($chunkSize < 1) {
            throw new UnexpectedValueException("Chunk size to less");
        }
        
        if ($skipHeader) {
            $this->source->getOne();
        }
        
        while ($this->source->hasNextRow() !== false) {
            $usersData  = $this->readChunk($chunkSize);
            $attributes = [];
            
            array_filter($usersData, function ($dataArray) use (&$attributes, $fieldsSet) {
                $attributesValues = array_intersect_key($dataArray, $fieldsSet);
                $userData         = array_combine(array_values($fieldsSet), array_values($attributesValues));
                $attributes[]     = $userData;
            });
            
            $this->userDataStorage->createMultiple($attributes);
        }
        
    }
    
    /**
     * @param int $count
     * @return array
     */
    private function readChunk(int $count)
    {
        $counter   = 0;
        $usersData = [];
        
        while ($this->source->hasNextRow() !== false && $counter !== $count) {
            $usersData[] = $this->source->getOne();
            $counter++;
        }
        
        return $usersData;
    }
    
    public function __destruct()
    {
        ini_set('max_execution_time', 30);
        set_time_limit(30);
    }
}
