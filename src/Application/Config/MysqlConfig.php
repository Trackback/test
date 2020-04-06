<?php namespace Core\Application\Config;

class MysqlConfig
{
    /**
     * @var string $dbServer
     */
    private $dbServer;
    /**
     * @var string $dbPort
     */
    private $dbPort;
    /**
     * @var string $dbPass
     */
    private $dbPass;
    /**
     * @var string $dbUser
     */
    private $dbUser;
    /**
     * @var string $dbName
     */
    private $dbName;

    /**
     * MysqlConfig constructor.
     * @param string $dbServer
     * @param string $dbPort
     * @param string $dbPass
     * @param string $dbUser
     * @param string $dbName
     */
    public function __construct(string $dbServer, string $dbPort, string $dbPass, string $dbUser, string $dbName)
    {
        $this->dbServer = $dbServer;
        $this->dbPort   = $dbPort;
        $this->dbPass   = $dbPass;
        $this->dbUser   = $dbUser;
        $this->dbName   = $dbName;
    }

    /**
     * @return string
     */
    public function getDbPass(): string
    {
        return $this->dbPass;
    }

    /**
     * @param string $dbPass
     */
    public function setDbPass($dbPass): void
    {
        $this->dbPass = $dbPass;
    }

    /**
     * @return string
     */
    public function getDbUser(): string
    {
        return $this->dbUser;
    }

    /**
     * @param string $dbUser
     */
    public function setDbUser($dbUser): void
    {
        $this->dbUser = $dbUser;
    }

    /**
     * @return string
     */
    public function getDbName(): string
    {
        return $this->dbName;
    }

    /**
     * @param string $dbName
     */
    public function setDbName($dbName): void
    {
        $this->dbName = $dbName;
    }

    /**
     * @return mixed
     */
    public function getDbServer()
    {
        return $this->dbServer;
    }

    /**
     * @param mixed $dbServer
     */
    public function setDbServer($dbServer)
    {
        $this->dbServer = $dbServer;
    }

    /**
     * @return mixed
     */
    public function getDbPort()
    {
        return $this->dbPort;
    }

    /**
     * @param mixed $dbPort
     */
    public function setDbPort($dbPort)
    {
        $this->dbPort = $dbPort;
    }
}
