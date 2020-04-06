<?php

namespace Application\Database;

use Core\Application\Config\MysqlConfig;
use Core\Application\Source\PdoAdapter;
use PDO;
use PHPUnit\Framework\TestCase;

class PdoAdapterTest extends TestCase
{
    protected static $adapter;
    
    public static function setUpBeforeClass(): void
    {
        $mysqlConnectionConfig = new MysqlConfig('localhost', '5432', '123456', 'root', 'test');
        self::$adapter         = new PdoAdapter($mysqlConnectionConfig);
    }
    
    public function testCreate()
    {
        $user   = [
            'name'  => 'testPDO',
            'email' => 'testEmail@test.com',
            'phone' => 123456789,
        ];
        $result = self::$adapter->create('users', $user);
        
        $this->assertEquals(true, $result);
        
        return $user;
    }
    
    /**
     * @depends testCreate
     */
    public function testLastInsertId($user)
    {
        $user['id'] = self::$adapter->lastInsertId();
        
        self::$adapter->select('users', ['*'], ['name' => $user['name']]);
        $lastInsertUser = self::$adapter->getOne();
        
        $this->assertEquals($user, $lastInsertUser);
        
        return $lastInsertUser;
    }
    
    /**
     * @depends testLastInsertId
     */
    public function testUpdate($user)
    {
        $result = self::$adapter->update('users', ['phone' => 9999], ['id' => $user['id']]);
        
        $this->assertEquals(true, $result);
        
        return $user;
    }
    
    /**
     * @depends testUpdate
     */
    public function testSelect($user)
    {
        self::$adapter->select('users', ['*'], ['id' => $user['id']]);
        $result = self::$adapter->getOne();
        
        $this->assertEquals($user['id'], $result['id']);
        
        return $result;
    }
    
    /**
     * @depends testSelect
     */
    public function testGetOne($user)
    {
        self::$adapter->select('users', ['*'], ['id' => $user['id']]);
        $result = self::$adapter->getOne();
        
        $this->assertCount(4, $result);
        
        return $result;
    }
    
    /**
     * @depends testGetOne
     */
    public function testDelete($user)
    {
        $result = self::$adapter->delete('users', ['id' => $user['id']]);
        
        $this->assertEquals(true, $result);
        
        self::$adapter->select('users', ['*'], ['id' => $user['id']]);
        $result = self::$adapter->getOne();
        
        $this->assertEquals([], $result);
    }
    
    public function testBeginTransaction()
    {
        $result = self::$adapter->beginTransaction();
        
        $this->assertEquals(true, $result);
        
        $user = [
            'name'  => 'testPDO',
            'email' => 'testEmail@test.com',
            'phone' => 123456789,
        ];
        
        self::$adapter->create('users', $user);
        $result = self::$adapter->create('users', $user);
        
        $this->assertEquals(true, $result);
        
        return $user;
    }
    
    /**
     * @depends testBeginTransaction
     */
    public function testCommit($user)
    {
        $result = self::$adapter->commit();
        
        $this->assertEquals(true, $result);
        
        return $user;
    }
    
    /**
     * @depends testCommit
     */
    public function testRowCount($user)
    {
        self::$adapter->select('users', ['*'], ['name' => $user['name']]);
        self::$adapter->getAll();
        $result = self::$adapter->rowCount();
        
        $this->assertEquals(2, $result);
        
        return $user;
    }
    
    /**
     * @depends testRowCount
     */
    public function testRollBack($user)
    {
        self::$adapter->select('users', ['*'], ['name' => $user['name']]);
        self::$adapter->getAll();
        $result = self::$adapter->rowCount();
        
        $this->assertEquals(2, $result);
        
        self::$adapter->beginTransaction();
        
        $result = self::$adapter->create('users', $user);
        
        $this->assertEquals(true, $result);
        
        $result = self::$adapter->rollBack();
        
        $this->assertEquals(true, $result);
        
        self::$adapter->select('users', ['*'], ['name' => $user['name']]);
        self::$adapter->getAll();
        $result = self::$adapter->rowCount();
        
        $this->assertEquals(2, $result);
        
        return $user;
    }
    
    /**
     * @depends testRollBack
     */
    public function testPrepare($user)
    {
        self::$adapter->prepare('SELECT * FROM users WHERE name = :name', ['name' => $user['name']]);
        
        $result = self::$adapter->execute();
        
        $this->assertEquals(true, $result);
        
        self::$adapter->getAll();
        $result = self::$adapter->rowCount();
        
        $this->assertEquals(2, $result);
        
        return $user;
    }
    
    /**
     * @depends testPrepare
     */
    public function testExecute($user)
    {
        self::$adapter->prepare('SELECT * FROM users WHERE name = :name', ['name' => $user['name']]);
        
        $result = self::$adapter->execute();
        
        $this->assertEquals(true, $result);
        
        self::$adapter->getAll();
        $result = self::$adapter->rowCount();
        
        $this->assertEquals(2, $result);
        
        return $user;
    }
    
    /**
     * @depends testExecute
     */
    public function testGetAll($user)
    {
        self::$adapter->select('users', ['*'], ['name' => $user['name']]);
        $result = self::$adapter->getAll();
        
        $this->assertCount(2, $result);
        
        return $result;
    }
    
    /**
     * @depends testExecute
     */
    public function testGetConnection()
    {
        $result = self::$adapter->getConnection();
        
        $this->assertInstanceOf(PDO::class, $result);
    }
    
    /**
     * @depends testGetConnection
     */
    public function testCloseConnection()
    {
        self::$adapter->closeConnection();
        $result = self::$adapter->getConnection();
        
        $this->assertNull($result);
    }
    
    public static function tearDownAfterClass(): void
    {
        if (self::$adapter->getConnection() == null) {
            self::$adapter         = null;
            $mysqlConnectionConfig = new MysqlConfig('localhost', '5432', '123456', 'root', 'test');
            self::$adapter         = new PdoAdapter($mysqlConnectionConfig);
        }
        
        self::$adapter->delete('users', ['name' => 'testPDO']);
    }
}
