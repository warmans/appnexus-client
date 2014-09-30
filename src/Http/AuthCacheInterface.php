<?php
namespace ANClient\Http;

interface AuthCacheInterface
{
    public function cacheToken($token);

    public function getToken();
}
