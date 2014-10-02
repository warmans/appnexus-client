<?php
namespace ANClient\Entity;

use ANClient\Entity;
use ANClient\Resource\AbstractResource;
use ANClient\Resource\PlacementResource;
use ANClient\Resource\SiteResource;

class Site extends Entity
{
    public function fetchPlacements(array $conditions = [], $limit = -1, $offset = 0)
    {
        return $this->fetchChildren(new PlacementResource($this->resource->getClient()), $conditions, $limit, $offset);
    }

    public function duplicate(AbstractResource $resource = null)
    {
        $entity = parent::duplicate($resource);

        unset($entity['placements']);

        return $entity;
    }
}
