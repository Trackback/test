<?php

namespace Core\Application\FileProcessing\Service;

use Core\Application\FileProcessing\Source\FileSourceInterface as FileSource;
use OutOfRangeException;
use RuntimeException;
use TypeError;

class FilesProcessingService implements FilesProcessingServiceInterface
{
    /**
     * @var FileSource $source ;
     */
    private $source;
    
    /**
     * @param FileSource $source
     */
    public function openFile(FileSource $source)
    {
        $this->source = $source;
        
        if (!$this->source->openFile()) {
            throw new RuntimeException("Error while opening file");
        }
    }
    
    /**
     * @return array
     */
    public function readRow(): array
    {
        return $this->source->readRow();
    }
    
    /**
     * @param int $count
     * @return array
     */
    public function readRows(int $count = 1): array
    {
        if ($this->source) {
            throw new RuntimeException("File is not opened");
        }
        
        $rowsList = [];
        
        for ($i = 0; $i < $count; $i++) {
            if (!$this->nextAvailable()) {
                throw new OutOfRangeException("Out of range");
            }
            
            $rowsList[] = $this->source->readRow();
        }
        
        return $this->source->readRow();
    }
    
    /**
     * @return bool
     */
    public function closeFile(): bool
    {
        if (!$this->source) {
            throw new RuntimeException("Can't close file, it is not open");
        }
        
        return $this->source->closeFile();
    }
    
    /**
     * @return bool
     */
    public function fileIsEmpty(): bool
    {
        if (!$this->source) {
            throw new RuntimeException("File is not opened");
        }
        
        return !($this->source->getCurrentPosition() > 0 || $this->nextAvailable());
    }
    
    /**
     * @return bool
     */
    public function nextAvailable(): bool
    {
        try {
            $currentPosition = $this->source->getCurrentPosition();
            $nextRow         = $this->readRow();
            $this->source->setPosition($currentPosition);
            
            return $nextRow ? true : false;
        } catch (TypeError $err) {
            return false;
        }
    }
    
    /**
     * @return void
     */
    public function __destruct()
    {
        $this->source->closeFile();
    }
}
