<?php
namespace ANClient\Auth;

interface TokenCacheInterface
{
    public function cacheToken($token);

    public function getToken();
}
