<?php
namespace ANClient\Auth;

/**
 * Just hold the auth token in memory for the duration of the scripts execution.
 *
 * @package ANClient\Http
 */
class MemoryTokenCache implements TokenCacheInterface
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
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }
}
