<?php

namespace Core\Application\DataProcessing\Import\Source;

use Core\Application\FileProcessing\Service\FilesProcessingServiceInterface as FileProcessingService;

class FileSource implements ImportSourceInterface
{
    /**
     * @var FileProcessingService
     */
    private $fileProcessingService;
    
    /**
     * FileSource constructor.
     * @param FileProcessingService $fileProcessingService
     */
    public function __construct(FileProcessingService $fileProcessingService)
    {
        $this->fileProcessingService = $fileProcessingService;
    }
    
    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->fileProcessingService->fileIsEmpty();
    }
    
    /**
     * @return array
     */
    public function getOne(): array
    {
        return $this->fileProcessingService->readRow();
    }
    
    /**
     * @return bool
     */
    public function hasNextRow(): bool
    {
        return $this->fileProcessingService->nextAvailable();
    }
    
}
