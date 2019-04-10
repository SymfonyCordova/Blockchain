<?php
namespace AppBundle\Handler;

class RedisSessionHandler implements \SessionHandlerInterface{

    /**
     * @var \Redis
     */
    private $redis;

    /**
     * @var string Key prefix for shared environments
     */
    private $prefix;

    /**
     * @var int Time to live in seconds
     */
    private $ttl;

    /**
     * RedisSessionHandler constructor.
     */
    public function __construct($redis, array $options = array()){
        if(!$redis instanceof \Redis &&
            !$redis instanceof \RedisArray &&
            !$redis instanceof \RedisCluster
        ){
            throw new \InvalidArgumentException(
                sprintf('%s() expects parameter 1 to be Redis, RedisArray, RedisCluster %s given',
                    __METHOD__, \is_object($redis) ? \get_class($redis) : \gettype($redis)));
        }

        if ($diff = array_diff(array_keys($options), array('prefix', 'expiretime'))) {
            throw new \InvalidArgumentException(sprintf('The following options are not supported "%s"', implode(', ', $diff)));
        }

        $this->redis = $redis;
        $this->ttl = isset($options['expiretime']) ? (int) $options['expiretime'] : 86400;
        $this->prefix = isset($options['prefix']) ? $options['prefix'] : 'symfony_session';
    }

    /**
     * session 关闭
     * @return bool
     */
    public function close()
    {
        return true;
    }

    public function destroy($sessionId)
    {
        $result = $this->redis->delete($this->prefix.$sessionId);

        return $result;
    }

    /**
     * 回收超时时间
     * @param int|string $maxlifetime
     * @return bool
     */
    public function gc($maxlifetime)
    {
        return true;
    }

    /**
     * session 打开
     * @param string $save_path
     * @param string $name
     * @return bool
     */
    public function open($save_path, $name)
    {
        return true;
    }

    public function read($sessionId)
    {
        return $this->redis->get($this->prefix.$sessionId) ? : '';
    }

    public function write($sessionId, $data)
    {
        return $this->redis->set($this->prefix.$sessionId, $data, time() + $this->ttl);
    }
}