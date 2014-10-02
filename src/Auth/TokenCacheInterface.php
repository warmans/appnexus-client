<?php
namespace ANClient\Auth;

/**
 * An authentication token is valid for several minutes after it has been generated. This interface can be implemented
 * by a persistent store e.g. redis to improve performance.
 *
 * @package ANClient\Auth
 */
interface TokenCacheInterface
{
    /**
     * @param $token
     * @return void
     */
    public function cacheToken($token);

    /**
     * @return string
     */
    public function getToken();
}
