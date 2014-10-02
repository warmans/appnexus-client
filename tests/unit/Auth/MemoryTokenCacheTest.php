<?php
namespace ANClient\Auth;

class MemoryTokenCacheTest extends \PHPUnit_Framework_TestCase
{
    public function testCacheGetToken()
    {
        $ob = new MemoryTokenCache();
        $ob->cacheToken('foo');

        $this->assertEquals('foo', $ob->getToken());
    }

    public function testReplaceCachedToken()
    {
        $ob = new MemoryTokenCache();
        $ob->cacheToken('foo');
        $ob->cacheToken('bar');
        $ob->cacheToken('baz');

        $this->assertEquals('baz', $ob->getToken());
    }
}
 