<?php
namespace ANClient\Client;

interface AuthCacheInterface
{
    public function cacheToken($token);

    public function getToken();
}
