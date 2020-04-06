<?php

namespace Application\DataProcessing\Import\Service;

use Core\Application\Config\MysqlConfig;
use Core\Application\DataProcessing\Import\Source\FileSource;
use Core\Application\DataProcessing\ImportUsers;
use Core\Application\DataStorage\Source\DatabaseStorage;
use Core\Application\DataStorage\UserDataStorage;
use Core\Application\FileProcessing\Service\FilesProcessingService;
use Core\Application\FileProcessing\Source\CSVFileSource;
use Core\Application\Source\PdoAdapter;
use InvalidArgumentException;
use LengthException;
use PHPUnit\Framework\TestCase;
use UnexpectedValueException;

class ImportUsersTest extends TestCase
{
    public static $importService;
    public static $filesProcessing;
    
    public static function setUpBeforeClass(): void
    {
        $mysqlConnectionConfig = new MysqlConfig('localhost', '5432', '123456', 'root', 'test');
        
        $mysqlAdapter          = new PdoAdapter($mysqlConnectionConfig);
        $databaseStorage       = new DatabaseStorage('users', $mysqlAdapter);
        $dataSource            = new UserDataStorage($databaseStorage);
        $fileSource            = new CSVFileSource(__DIR__ . DIRECTORY_SEPARATOR . 'not_empty.csv');
        self::$filesProcessing = new FilesProcessingService();
        self::$filesProcessing->openFile($fileSource);
        
        $fileSourceForImport = new FileSource(self::$filesProcessing);
        self::$importService = new ImportUsers($fileSourceForImport, $dataSource);
    }
    
    public function testImport()
    {
        $fieldSet = [1 => 'name', 2 => 'phone', 3 => 'email'];
        self::$importService->import($fieldSet, 2, true);
        
        $mysqlConfig = new MysqlConfig('localhost', '5432', '123456', 'root', 'test');
        $adapter     = new PdoAdapter($mysqlConfig);
        
        $adapter->select('users', ['*'], ['name' => 'ImportedTestName']);
        $adapter->getAll();
        $result = $adapter->rowCount();
        
        $this->assertEquals(3, $result);
    }
    
    
    public function testImportWithoutFieldSet()
    {
        $this->expectException(InvalidArgumentException::class);
        
        self::$importService->import([]);
    }
    
    public function testImportWithLessChunk()
    {
        $fieldSet = [1 => 'name', 2 => 'phone', 3 => 'email'];
        
        $this->expectException(UnexpectedValueException::class);
        
        self::$importService->import($fieldSet, 0);
    }
    
    public function testImportWithEmptyFile()
    {
        $this->expectException(LengthException::class);
        
        $fileSource = new CSVFileSource(__DIR__ . DIRECTORY_SEPARATOR . 'empty.csv');
        self::$filesProcessing->closeFile();
        self::$filesProcessing->openFile($fileSource);
        
        $fieldSet = [1 => 'name', 2 => 'phone', 3 => 'email'];
        
        self::$importService->import($fieldSet, 2, true);
    }
    
    public static function tearDownAfterClass(): void
    {
        $mysqlConfig = new MysqlConfig('localhost', '5432', '123456', 'root', 'test');
        $adapter     = new PdoAdapter($mysqlConfig);
        
        $adapter->delete('users', ['name' => 'ImportedTestName']);
    }
}
