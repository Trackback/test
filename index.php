<?php
ini_set('display_errors', 1);

require_once('vendor/autoload.php');

use Core\Application\Config\MysqlConfig;
use Core\Application\DataProcessing\Import\Source\FileSource;
use Core\Application\DataProcessing\ImportUsers;
use Core\Application\DataStorage\Source\DatabaseStorage;
use Core\Application\DataStorage\UserDataStorage;
use Core\Application\FileProcessing\Service\FilesProcessingService;
use Core\Application\FileProcessing\Source\CSVFileSource;
use Core\Application\Source\PdoAdapter;

echo "Begin";

$mysqlConnectionConfig = new MysqlConfig('localhost', '5432', '123456', 'root', 'test');

$mysqlAdapter    = new PdoAdapter($mysqlConnectionConfig);
$databaseStorage = new DatabaseStorage('users', $mysqlAdapter);
$dataSource      = new UserDataStorage($databaseStorage);
$fileSource      = new CSVFileSource(__DIR__ . DIRECTORY_SEPARATOR .'data.csv');
$filesProcessing = new FilesProcessingService();
$filesProcessing->openFile($fileSource);

$fileSourceForImport = new FileSource($filesProcessing);
$import              = new ImportUsers($fileSourceForImport, $dataSource);
$import->import([1 => 'name', 2 => 'phone', 3 => 'email'], 100, true);

echo "Done";
