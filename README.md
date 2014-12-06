PHP Appnexus API Client
==========================

[![Build Status](https://travis-ci.org/warmans/appnexus-client.svg?branch=master)](https://travis-ci.org/warmans/appnexus-client)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/warmans/appnexus-client/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/warmans/appnexus-client/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/warmans/appnexus-client/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/warmans/appnexus-client/?branch=master)


Client for use the non-public appnexus API.

## Basic Useage

This client attempts to represent the appnexus data available as a series of Resources which return Entities.
For example /publisher is the resource that returns a list of publisher objects. As such there is a PublisherResource
which will return a list of Publisher Entity instances.

```php
//log to stdout
$log = new Logger('client');
$log->pushHandler(new StreamHandler(STDOUT, Logger::DEBUG));

//create a HTTP client
$http = new GuzzleHttp\Client();
$http->getEmitter()->attach(new LogSubscriber($log, Formatter::CLF));

$client = new \ANClient\Client(
    array(
        'endpoint' => 'http://sand.api.appnexus.com',
        'auth' => array(
            'username' => 'YOUR USERNAME',
            'password' => 'YOUR PASSWORD'
        )
    ),
    $http
);

//create an instance of the publisher resource
$publisherResource = new PublisherResource($client);

//fetch the first 10 publishers
foreach ($publisherResource->fetch([], 10, 0) as $publisher) {
    var_dump($publisher['id']);
}
```

Entities attempt to represent the relationships between resources with convenience methods such as Publisher::fetchSites()
however if a relationship is not hard-coded into the entity it is possible to do the same thing manually. The following
two examples should result in the same thing happening:

Fetch using convenience method:

```php
...[create client etc]

//create an instance of the publisher resource
$publisherResource = new PublisherResource($client);

$publisher = $publisherResource->fetchId(1); //fetch publisher with id 1
var_dump($publisher->fetchSites());

```

What actually happens:

```php
...[create client etc]

//create an instance of the publisher resource
$publisherResource = new PublisherResource($client);

$publisher = $publisherResource->fetchId(1); //fetch publisher with id 1
var_dump($publisher->fetchChildren(new \ANClient\Resource\SiteResource($client));

```

## Duplicating Entities

This client was originally written to migrate publishers between appnexus accounts. As such entities have a duplicate
method which attempts to create a new "clean" version of the entity with any inappropriate attributes stripped out e.g.
IDs which might not exist in the new entity's account. AS such if you are simply duplicating entities for the SAME
account it may be easier to just manually duplicate the entity and ONLY unset the entity ID.

## Auth Token Caching

It may not be desirable to create a new token for every request as such tokens are stored via an implementation of the
Auth\TokenCacheInterface. Currently only array storage is provided which does not cache anything however it is trivial
to implement the interface using redis, memcache or just file storage.

## Examples

Further examples are provided in the /examples directory.

