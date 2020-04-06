<?php

namespace Application\DataProcessing\Import\Source;

use Core\Application\DataProcessing\Import\Source\FileSource;
use Core\Application\FileProcessing\Service\FilesProcessingService;
use Core\Application\FileProcessing\Source\CSVFileSource;
use PHPUnit\Framework\TestCase;

class FileSourceTest extends TestCase
{
    protected static $fileSource;
    
    public static function setUpBeforeClass(): void
    {
        $fileSource      = new CSVFileSource(__DIR__ . DIRECTORY_SEPARATOR . 'not_empty.csv');
        $filesProcessing = new FilesProcessingService();
        $filesProcessing->openFile($fileSource);
        
        self::$fileSource = new FileSource($filesProcessing);
    }
    
    public function testGetOne()
    {
        $result = self::$fileSource->getOne();
        
        $this->assertCount(4, $result);
    }
    
    public function testIsEmpty()
    {
        $result = self::$fileSource->isEmpty();
        
        $this->assertEquals(false, $result);
    }
    
    public function testHasNextRow()
    {
        $result = self::$fileSource->hasNextRow();
        
        $this->assertEquals(true, $result);
    }
    
    public static function tearDownAfterClass(): void
    {
    }
}
