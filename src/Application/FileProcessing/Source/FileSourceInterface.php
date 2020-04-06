<?php

namespace Core\Application\FileProcessing\Source;

interface FileSourceInterface
{
    /**
     * @return bool
     */
    public function openFile(): bool;
    
    /**
     * @return bool
     */
    public function closeFile(): bool;
    
    /**
     * @return array
     */
    public function readRow(): array;
    
    /**
     * @return int
     */
    public function getCurrentPosition(): int;
    
    /**
     * @param int $position
     */
    public function setPosition(int $position): void;
}
