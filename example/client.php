<?php
require_once dirname(__DIR__).'/vendor/autoload.php';

use GuzzleHttp\Subscriber\Log\Formatter;
use GuzzleHttp\Subscriber\Log\LogSubscriber;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

if (!is_file(__DIR__.'/config.php')) {
    throw new \RuntimeException('Create a config file (config.php) to run examples');
}

$config = require_once __DIR__.'/config.php';

$log = new Logger('name');
$log->pushHandler(new StreamHandler(STDOUT, Logger::DEBUG));

$http = new GuzzleHttp\Client();
$http->getEmitter()->attach(new LogSubscriber($log, Formatter::CLF));

return new \ANClient\Client($config, $http);