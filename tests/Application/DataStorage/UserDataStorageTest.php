<?php

namespace Application\DataStorage;

use Core\Application\Config\MysqlConfig;
use Core\Application\DataStorage\Source\DatabaseStorage;
use Core\Application\DataStorage\UserDataStorage;
use Core\Application\Source\PdoAdapter;
use PHPUnit\Framework\TestCase;

class UserDataStorageTest extends TestCase
{
    protected static $dataSource;
    
    public static function setUpBeforeClass(): void
    {
        $mysqlConnectionConfig = new MysqlConfig('localhost', '5432', '123456', 'root', 'test');
        $mysqlAdapter          = new PdoAdapter($mysqlConnectionConfig);
        $databaseStorage       = new DatabaseStorage('users', $mysqlAdapter);
        self::$dataSource      = new UserDataStorage($databaseStorage);
    }
    
    public function testCreate()
    {
        $userData = [
            'name'  => 'testName',
            'email' => 'testEmail@test.com',
            'phone' => 123,
        ];
        
        $result = self::$dataSource->create($userData);
        
        $this->assertEquals(true, $result);
        
        return $userData;
    }
    
    /**
     * @depends testCreate
     */
    public function testFind($userData)
    {
        $userByName     = self::$dataSource->find(['name' => $userData['name']]);
        $userData['id'] = $userByName['id'];
        
        $this->assertEquals($userData, $userByName);
        
        return $userByName;
    }
    
    /**
     * @depends testFind
     */
    public function testUpdate($userData)
    {
        $result = self::$dataSource->update(['id' => $userData['id']], ['name' => 'testNameUpdated']);
        
        $this->assertEquals(true, $result);
        
        return $userData;
    }
    
    /**
     * @depends testUpdate
     */
    public function testFindById($userData)
    {
        $userById = self::$dataSource->findById($userData['id']);
        
        $this->assertEquals($userData['id'], $userById['id']);
        
        return $userById;
    }
    
    /**
     * @depends testFindById
     */
    public function testCreateMultiple($user)
    {
        $userData = [
            [
                'name'  => 'testNameUpdated',
                'email' => 'testEmail@test.com',
                'phone' => 123,
            ],
            [
                'name'  => 'testNameUpdated',
                'email' => 'testEmail2@test.com',
                'phone' => 456,
            ],
        ];
        
        $result = self::$dataSource->createMultiple($userData);
        
        $this->assertEquals(true, $result);
        
        return $user;
    }
    
    /**
     * @depends testCreateMultiple
     */
    public function testDelete()
    {
        $result = self::$dataSource->delete(['name' => 'testNameUpdated']);
        
        $this->assertEquals(true, $result);
    }
    
    public static function tearDownAfterClass(): void
    {
        self::$dataSource->delete(['name' => 'testNameUpdated']);
        self::$dataSource->delete(['name' => 'testName']);
    }
}
