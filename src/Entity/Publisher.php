<?php
namespace ANClient\Entity;

use ANClient\Entity;
use ANClient\Resource\AbstractResource;
use ANClient\Resource\SiteResource;

class Publisher extends Entity
{
    public function fetchSites(array $conditions = [], $limit = 99, $offset = 0)
    {
        return $this->fetchChildren(new SiteResource($this->resource->getClient()), $conditions, $limit, $offset);
    }

    public function fetchAllSites(array $conditions = [])
    {
        return $this->fetchAllChildren(new SiteResource($this->resource->getClient()), $conditions);
    }

    public function duplicate(AbstractResource $resource = null)
    {
        $entity = parent::duplicate($resource);

        unset($entity['base_payment_rule_id']);
        unset($entity['base_ad_quality_rule_id']);
        unset($entity['sites']);

        return $entity;
    }
}
