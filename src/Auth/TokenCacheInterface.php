<?php
namespace ANClient\Auth;

interface TokenCacheInterface
{
    /**
     * @return void
     */
    public function cacheToken($token);

    /**
     * @return string
     */
    public function getToken();
}
