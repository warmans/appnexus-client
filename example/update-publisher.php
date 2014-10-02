<?php
use ANClient\Resource\PublisherResource;

//UPDATE THIS
const PUBLISHER_TO_UPDATE = 0;

$client = require_once __DIR__.'/client.php';

$publisherResource = new PublisherResource($client);

$publisher = $publisherResource->fetchId(PUBLISHER_TO_UPDATE);
$publisher['name'] = $publisher['name'].' [upd]';

$publisher->persist();