<?php

namespace Core\Application\Source;

use Core\Application\Config\MysqlConfig;
use PDO;
use PDOException;
use PDOStatement;
use RuntimeException;

class PdoAdapter implements DatabaseAdapterInterface
{
    /**
     * @var PDO $PDOInstance
     */
    static private $PDOInstance;
    
    /**
     * @var PDOStatement $statement
     */
    private $statement;
    
    /**
     * @var bool $connected
     */
    private $connected = false;
    
    /**
     * @param MysqlConfig $config
     * @throws RuntimeException
     */
    public function __construct(MysqlConfig $config)
    {
        $connection = 'mysql:host=' . $config->getDbServer() . ';port=' . $config->getDbPort() . ';dbname=' . $config->getDbName();
        
        if (!self::$PDOInstance) {
            try {
                self::$PDOInstance = new PDO($connection, $config->getDbUser(), $config->getDbPass());
                
                self::$PDOInstance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$PDOInstance->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
                
                $this->connected = true;
                
                return self::$PDOInstance;
            } catch (PDOException $e) {
                throw new RuntimeException("Failed to connect to DB: " . $e->getMessage());
            }
        }
        
        return self::$PDOInstance;
    }
    
    /**
     * @param string $sql
     * @param array  $params
     * @return void
     */
    public function prepare(string $sql, array $params = []): void
    {
        $this->statement = self::$PDOInstance->prepare($sql);
        
        if (!empty($params)) {
            $this->bindParams($params);
        }
    }
    
    /**
     * @param string $param
     * @param mixed  $value
     * @param null   $type
     */
    private function bind(string $param, $value, $type = null): void
    {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
            $this->statement->bindValue(':' . $param, $value, $type);
        }
    }
    
    
    /**
     * @param array  $params
     * @param string $prefix
     */
    private function bindParams(array $params, string $prefix = ''): void
    {
        foreach ($params as $key => $value) {
            $this->bind($prefix . $key, $value);
        }
    }
    
    /**
     * @return bool
     */
    public function execute(): bool
    {
        return $this->statement->execute();
    }
    
    /**
     * @return array
     */
    public function getAll(): array
    {
        $mode = PDO::FETCH_ASSOC;
        $this->execute();
        
        $result = $this->statement->fetchAll($mode);
        
        return $result ?: [];
    }
    
    /**
     * @return array
     */
    public function getOne(): array
    {
        $mode = PDO::FETCH_ASSOC;
        $this->execute();
        
        $result = $this->statement->fetch($mode);
        
        return $result ?: [];
    }
    
    /**
     * @return int
     */
    public function rowCount(): int
    {
        return $this->statement->rowCount();
    }
    
    /**
     * @param string $table
     * @param array  $conditions
     * @return bool
     */
    public function delete(string $table, array $conditions): bool
    {
        $conditionsQuery = [];
        
        foreach ($conditions as $condition => $value) {
            $conditionsQuery[] = "{$condition} = :where_{$condition}";
        }
        
        $whereQuery = implode(' AND ', $conditionsQuery);
        
        $this->prepare("DELETE FROM {$table}  WHERE {$whereQuery}");
        
        $this->bindParams($conditions, 'where_');
        
        return $this->execute();
    }
    
    /**
     * @param string $table
     * @param array  $attributes
     * @param array  $conditions
     * @return bool
     */
    public function update(string $table, array $attributes, array $conditions): bool
    {
        $valuesQuery     = '';
        $conditionsQuery = [];
        
        foreach ($attributes as $field => $value) {
            $valuesQuery .= "{$field}=:param_{$field},";
        }
        
        $valuesQuery = substr($valuesQuery, 0, -1);
        
        foreach ($conditions as $condition => $value) {
            $conditionsQuery[] = "{$condition} = :where_{$condition}";
        }
        
        $whereQuery = implode(' AND ', $conditionsQuery);
        
        $this->prepare("UPDATE {$table} SET {$valuesQuery} WHERE {$whereQuery}");
        
        $this->bindParams($attributes, 'param_');
        $this->bindParams($conditions, 'where_');
        
        return $this->execute();
    }
    
    /**
     * @param string $table
     * @param array  $fields
     * @param array  $conditions
     */
    public function select(string $table, array $fields, array $conditions): void
    {
        $conditionsQuery = [];
        
        foreach ($conditions as $condition => $value) {
            $conditionsQuery[] = "{$condition} = :where_{$condition}";
        }
        
        $fieldsQuery = implode(', ', $fields);
        $whereQuery  = implode(' AND ', $conditionsQuery);
        
        $this->prepare("SELECT {$fieldsQuery} FROM {$table}  WHERE {$whereQuery}");
        
        $this->bindParams($conditions, 'where_');
    }
    
    /**
     * @param string $table
     * @param array  $attributes
     * @return bool
     */
    public function create($table, $attributes): bool
    {
        $fields = '';
        $values = '';
        
        foreach ($attributes as $attribute => $value) {
            $fields .= "{$attribute},";
            $values .= ":{$attribute},";
        }
        
        $fields = substr($fields, 0, -1);
        $values = substr($values, 0, -1);
        
        $this->prepare("INSERT INTO {$table} ({$fields}) VALUES ({$values})");
        $this->bindParams($attributes);
        
        return $this->execute();
    }
    
    /**
     * @return int
     */
    public function lastInsertId(): int
    {
        return self::$PDOInstance->lastInsertId();
    }
    
    /**
     *
     */
    public function closeConnection(): void
    {
        self::$PDOInstance = null;
    }
    
    /**
     * @return PDO|null
     */
    public function getConnection(): ?PDO
    {
        return self::$PDOInstance;
    }
    
    /**
     * @return bool
     */
    public function beginTransaction(): bool
    {
        return $this->getConnection()->beginTransaction();
    }
    
    /**
     * @return bool
     */
    public function commit(): bool
    {
        return $this->getConnection()->commit();
    }
    
    /**
     * @return bool
     */
    public function rollBack(): bool
    {
        return $this->getConnection()->rollBack();
    }
    
    /**
     * @return  void
     */
    public function __destruct()
    {
        $this->closeConnection();
    }
}
