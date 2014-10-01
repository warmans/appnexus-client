<?php
namespace ANClient\Entity;

use ANClient\Entity;
use ANClient\Resource\AbstractResource;
use ANClient\Resource\AdProfileResource;
use ANClient\Resource\PlacementResource;
use ANClient\Resource\SiteResource;

class AdQualityRule extends Entity
{
    public function fetchAdProfile()
    {
        $profileRes = new AdProfileResource($this->resource->getClient());
        return $profileRes->fetchId($this['ad_profile_id']);
    }
}
