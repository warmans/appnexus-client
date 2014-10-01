<?php
use ANClient\Resource\PublisherResource;

$client = require_once __DIR__.'/client.php';

$publisherResource = new PublisherResource($client);
var_dump($publisherResource->fetch());
