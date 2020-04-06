<?php

namespace Core\Application\FileProcessing\Service;

use Core\Application\FileProcessing\Source\FileSourceInterface as FileSource;

interface FilesProcessingServiceInterface
{
    /**
     * @return mixed
     */
    public function fileIsEmpty();
    
    /**
     * @param FileSource $source
     * @return mixed
     */
    public function openFile(FileSource $source);
    
    /**
     * @return array
     */
    public function readRow(): array;
    
    /**
     * @param int $count
     * @return mixed
     */
    public function readRows(int $count);
    
    /**
     * @return mixed
     */
    public function closeFile();
    
    /**
     * @return mixed
     */
    public function nextAvailable();
}
