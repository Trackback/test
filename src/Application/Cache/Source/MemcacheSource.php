<?php

namespace Core\Application\Cache\Source;

use Memcache;

class MemcacheSource implements CacheSourceInterface
{
    /**
     * @var Memcache $memcache
     */
    private $memcache;
    
    public function __construct()
    {
        $this->memcache = new Memcache();
    }
    
    /**
     * @param string $host
     * @param int    $port
     * @return bool
     */
    public function addServer(string $host, int $port): bool
    {
        return $this->memcache->addServer($host, $port);
    }
    
    /**
     * @param string $key
     * @return array|false|string
     */
    public function get(string $key)
    {
        return $this->memcache->get($key);
    }
    
    /**
     * @param string $key
     * @param        $value
     * @param int    $expire
     * @param bool   $compress
     * @return bool
     */
    public function put(string $key, $value, int $expire = 1800, bool $compress = false): bool
    {
        return $this->memcache->set($key, $value, $compress, $expire);
    }
    
    /**
     * @param string $key
     * @param        $value
     * @param int    $expire
     * @param bool   $compress
     * @return bool
     */
    public function update(string $key, $value, int $expire = 1800, bool $compress = false): bool
    {
        return $this->memcache->replace($key, $value, $compress, $expire);
    }
    
    /**
     * @param string $key
     * @return bool
     */
    public function delete(string $key): bool
    {
        return $this->memcache->delete($key);
    }
    
    /**
     * @param $key
     * @return bool
     */
    public function present($key): bool
    {
        return $this->get($key) !== false;
    }
}
