<?php
namespace ANClient;

use ANClient\Auth\MemoryTokenCache;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Client
     */
    private $object;

    private $config;

    private $tokenCache;

    private $guzzleMock;

    public function setUp()
    {
        $this->config = [
            'endpoint' => 'http://foo.com',
            'auth' => [
                'username' => 'foo',
                'password' => 'bar'
            ]
        ];

        $this->guzzleMock = $this->getMockBuilder('\\GuzzleHttp\\Client')->disableOriginalConstructor()->getMock();

        $this->tokenCache = new MemoryTokenCache();

        $this->object = new Client($this->config, $this->guzzleMock, $this->tokenCache);
    }

    protected function getFakeResponse($statusCode, $body = null)
    {
        $response = $this->getMock('\\GuzzleHttp\\Message\\ResponseInterface');
        $response->expects($this->any())->method('getStatusCode')->will($this->returnValue($statusCode));

        if ($body) {
            $response->expects($this->any())->method('json')->will($this->returnValue($body));
        }

        return $response;
    }

    protected function getFakeRequest()
    {
        return $this->getMock('\\GuzzleHttp\\Message\\RequestInterface');
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testExceptionOnConstructWithInvalidConfig()
    {
        $ob = new Client(array(), $this->guzzleMock);
    }

    public function testGetHttpClientReturnsConfiguredClient()
    {
        $this->assertSame($this->guzzleMock, $this->object->getHttpClient());
    }

    public function testDispatchMethodIsSet()
    {
        $this->tokenCache->cacheToken('foo');

        $this->guzzleMock
            ->expects($this->any())
            ->method('createRequest')
            ->with($this->equalTo('POST'), $this->anything(), $this->anything())
            ->will($this->returnValue($this->getFakeRequest()));

        $this->guzzleMock
            ->expects($this->any())
            ->method('send')
            ->will($this->returnValue($this->getFakeResponse(200, ['response'=>[]])));

        $this->object->dispatch('POST');
    }

    public function testDispatchFullUriIsSet()
    {
        $this->tokenCache->cacheToken('foo');

        $this->guzzleMock
            ->expects($this->any())
            ->method('createRequest')
            ->with($this->anything(), $this->equalTo($this->config['endpoint'].'/bar'), $this->anything())
            ->will($this->returnValue($this->getFakeRequest()));

        $this->guzzleMock
            ->expects($this->any())
            ->method('send')
            ->will($this->returnValue($this->getFakeResponse(200, ['response'=>[]])));

        $this->object->dispatch('GET', 'bar');
    }

    public function testDispatchOptionsAreSet()
    {
        $this->tokenCache->cacheToken('foo');

        $this->guzzleMock
            ->expects($this->any())
            ->method('createRequest')
            ->with($this->anything(), $this->anything(), $this->equalTo(['foo'=>'bar']))
            ->will($this->returnValue($this->getFakeRequest()));

        $this->guzzleMock
            ->expects($this->any())
            ->method('send')
            ->will($this->returnValue($this->getFakeResponse(200, ['response'=>[]])));

        $this->object->dispatch('GET', null, ['foo'=>'bar']);
    }

    public function testDispatchAttachesAuthTokenToRequests()
    {
        $this->tokenCache->cacheToken('foo');

        $request = $this->getFakeRequest();
        $request->expects($this->once())
            ->method('setHeader')
            ->with($this->equalTo('Authorization'), $this->equalTo($this->tokenCache->getToken()));

        $this->guzzleMock
            ->expects($this->any())
            ->method('createRequest')
            ->will($this->returnValue($request));

        $this->guzzleMock
            ->expects($this->any())
            ->method('send')
            ->will($this->returnValue($this->getFakeResponse(200, ['response'=>[]])));

        $this->object->dispatch();
    }

    public function testGetAuthTokenUsesPostRequest()
    {
        $this->guzzleMock
            ->expects($this->any())
            ->method('createRequest')
            ->with($this->equalTo('POST'), $this->anything(), $this->anything())
            ->will($this->returnValue($this->getFakeRequest()));

        $this->guzzleMock
            ->expects($this->any())
            ->method('send')
            ->will($this->returnValue($this->getFakeResponse(200, ['response'=>['token'=>'foo']])));

        $this->object->getAuthToken();
    }

    public function testGetAuthTokenUsesAuthEndpoint()
    {
        $this->guzzleMock
            ->expects($this->any())
            ->method('createRequest')
            ->with($this->anything(),$this->equalTo($this->config['endpoint'].'/auth'), $this->anything())
            ->will($this->returnValue($this->getFakeRequest()));

        $this->guzzleMock
            ->expects($this->any())
            ->method('send')
            ->will($this->returnValue($this->getFakeResponse(200, ['response'=>['token'=>'foo']])));

        $this->object->getAuthToken();
    }

    public function testGetAuthTokenSendsCorrectBody()
    {
        $validAuth = ['auth' => $this->config['auth']];

        $this->guzzleMock
            ->expects($this->any())
            ->method('createRequest')
            ->with($this->anything(), $this->anything(), $this->equalTo(['json' => $validAuth]))
            ->will($this->returnValue($this->getFakeRequest()));

        $this->guzzleMock
            ->expects($this->any())
            ->method('send')
            ->will($this->returnValue($this->getFakeResponse(200, ['response'=>['token'=>'foo']])));

        $this->object->getAuthToken();
    }

    public function testGetAuthTokenCachesToken()
    {
        $validAuth = ['auth' => $this->config['auth']];

        $this->guzzleMock
            ->expects($this->any())
            ->method('createRequest')
            ->will($this->returnValue($this->getFakeRequest()));

        $this->guzzleMock
            ->expects($this->any())
            ->method('send')
            ->will($this->returnValue($this->getFakeResponse(200, ['response'=>['token'=>'foo']])));

        $this->object->getAuthToken();

        $this->assertEquals('foo', $this->tokenCache->getToken());
    }

    public function testGetAuthTokenReturnsToken()
    {
        $validAuth = ['auth' => $this->config['auth']];

        $this->guzzleMock
            ->expects($this->any())
            ->method('createRequest')
            ->will($this->returnValue($this->getFakeRequest()));

        $this->guzzleMock
            ->expects($this->any())
            ->method('send')
            ->will($this->returnValue($this->getFakeResponse(200, ['response'=>['token'=>'foo']])));

        $this->assertEquals('foo', $this->object->getAuthToken());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testResponseToResultExceptionOnNon200Status()
    {
        $this->object->responseToResult($this->getFakeResponse(500));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testResponseToResultExceptionOnNoResponseElement()
    {
        $this->object->responseToResult($this->getFakeResponse(200, []));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testResponseToResultExceptionOnErrorElement()
    {
        $this->object->responseToResult($this->getFakeResponse(200, ['response' => ['error'=>'foo']]));
    }

    public function testResponseToResultReturnsResponseElement()
    {
        $result = $this->object->responseToResult($this->getFakeResponse(200, ['response' => ['foo'=>'bar']]));

        $this->assertEquals(['foo'=>'bar'], $result);
    }
}
