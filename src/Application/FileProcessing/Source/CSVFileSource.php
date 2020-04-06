<?php

namespace Core\Application\FileProcessing\Source;

use RuntimeException;

/**
 * Class CSVFileSource
 * @package Core\Application\FileProcessing\Source
 */
class CSVFileSource implements FileSourceInterface
{
    /**
     * @var string $filePath
     */
    private $filePath;
    
    /**
     * @var string $delimiter
     */
    private $delimiter;
    /**
     * @var string $enclosure
     */
    private $enclosure;
    /**
     * @var string $escape
     */
    private $escape;
    
    /**
     * @var resource $fileSource ;
     */
    private $fileSource;
    
    /**
     * @param string $filePath
     * @param string $delimiter
     * @param string $enclosure
     * @param string $escape
     * @throws RuntimeException
     */
    public function __construct(
        string $filePath,
        string $delimiter = ",",
        string $enclosure = '"',
        string $escape = "\\"
    ) {
        $this->filePath  = $filePath;
        $this->delimiter = $delimiter;
        $this->enclosure = $enclosure;
        $this->escape    = $escape;
        
        if (!is_readable($this->filePath)) {
            throw new RuntimeException('File not found or is not readable');
        }
    }
    
    /**
     * @return bool
     */
    public function openFile(): bool
    {
        $this->fileSource = fopen($this->filePath, "r");
        
        return $this->fileSource ? true : false;
    }
    
    /**
     * @return bool
     * @throws RuntimeException
     */
    public function closeFile(): bool
    {
        if (!$this->fileSource) {
            throw new RuntimeException('File is not opened');
        }
        
        return fclose($this->fileSource);
    }
    
    /**
     * @return array
     * @throws RuntimeException
     */
    public function readRow(): array
    {
        if (!$this->fileSource) {
            throw new RuntimeException('File is not opened');
        }
        
        return fgetcsv($this->fileSource, 0, $this->delimiter, $this->enclosure, $this->escape);
    }
    
    /**
     * @return false|int
     * @throws RuntimeException
     */
    public function getCurrentPosition(): int
    {
        if (!$this->fileSource) {
            throw new RuntimeException('File is not opened');
        }
        
        return ftell($this->fileSource);
    }
    
    /**
     * @param int $position
     */
    public function setPosition(int $position): void
    {
        if (fseek($this->fileSource, $position, SEEK_SET) === -1) {
            throw new RuntimeException('Error while set read position');
        }
    }
}
