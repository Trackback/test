<?php

namespace Application\Cache;

use Core\Application\Cache\CacheService;
use Core\Application\Cache\Source\MemcacheSource;
use Core\Application\Config\MysqlConfig;
use Core\Application\DataStorage\Source\DatabaseStorage;
use Core\Application\DataStorage\UserDataStorage;
use Core\Application\Source\PdoAdapter;
use PHPUnit\Framework\TestCase;

class CacheServiceTest extends TestCase
{
    
    protected static $cacheService;
    
    public static function setUpBeforeClass(): void
    {
        $mysqlConnectionConfig = new MysqlConfig('localhost', '5432', '123456', 'root', 'test');
        $mysqlAdapter          = new PdoAdapter($mysqlConnectionConfig);
        $databaseStorage       = new DatabaseStorage('users', $mysqlAdapter);
        $cacheSource           = new MemcacheSource();
        $cacheSource->addServer('localhost', 11211);
        self::$cacheService = new CacheService($cacheSource, $databaseStorage);
    }
    
    public function testCreate()
    {
        $userData = [
            'name'  => 'testCacheName',
            'email' => 'testEmail@test.com',
            'phone' => 123,
        ];
        
        $result = self::$cacheService->create($userData);
        
        $this->assertEquals(true, $result);
        
        return $userData;
    }
    
    /**
     * @depends testCreate
     */
    public function testFind($userData)
    {
        $userByName     = self::$cacheService->find(['name' => $userData['name']]);
        $userData['id'] = $userByName['id'];
        
        $this->assertEquals($userData, $userByName);
        
        return $userByName;
    }
    
    /**
     * @depends testFind
     */
    public function testUpdate($userData)
    {
        $result = self::$cacheService->update(['id' => $userData['id']], ['name' => 'testCacheNameUpdated']);
        
        $this->assertEquals(true, $result);
        
        return $userData;
    }
    
    /**
     * @depends testUpdate
     */
    public function testCreateMultiple($user)
    {
        $userData = [
            [
                'name'  => 'testCacheName',
                'email' => 'testEmail@test.com',
                'phone' => 123,
            ],
            [
                'name'  => 'testCacheName',
                'email' => 'testEmail2@test.com',
                'phone' => 456,
            ],
        ];
        
        $result = self::$cacheService->createMultiple($userData);
        
        $this->assertEquals(true, $result);
        
        return $user;
    }
    
    /**
     * @depends testCreateMultiple
     */
    public function testFindAll($userData)
    {
        $allUsers = self::$cacheService->findAll(['name' => 'testCacheName']);
        
        $this->assertCount(4, $allUsers);
        
        return $userData;
    }
    
    /**
     * @depends testFindAll
     */
    public function testDelete($userData)
    {
        $allUsers = self::$cacheService->findAll(['name' => $userData['name']]);
        
        $this->assertCount(4, $allUsers);
        
        $result = self::$cacheService->delete(['name' => $userData['name']]);
        
        $this->assertEquals(true, $result);
        
        $allUsers = self::$cacheService->findAll(['name' => $userData['name']]);
        
        $this->assertCount(0, $allUsers);
    }
    
    public static function tearDownAfterClass(): void
    {
        self::$cacheService->delete(['name' => 'testCacheNameUpdated']);
        self::$cacheService->delete(['name' => 'testCacheName']);
    }
}
