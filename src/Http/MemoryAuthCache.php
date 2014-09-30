<?php
namespace ANClient\Http;

/**
 * Just hold the auth token in memory for the duration of the scripts execution.
 *
 * @package ANClient\Http
 */
class MemoryAuthCache implements AuthCacheInterface
{
    private $token = null;

    /**
     * @param string $token
     */
    public function cacheToken($token)
    {
        $this->token = $token;
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }
}
