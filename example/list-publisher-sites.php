<?php
use ANClient\Resource\PublisherResource;
use ANClient\Resource\SiteResource;

$client = require_once __DIR__.'/client.php';

$publisherResource = new PublisherResource($client);

foreach ($publisherResource->fetch([], 1) as $publisher) {
    var_dump($publisher->fetchSites());
}
