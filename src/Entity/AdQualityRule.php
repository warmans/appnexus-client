<?php
namespace ANClient\Entity;

use ANClient\Entity;
use ANClient\Resource\AdProfileResource;

class AdQualityRule extends Entity
{
    public function fetchAdProfile()
    {
        $profileRes = new AdProfileResource($this->resource->getClient());
        return $profileRes->fetchId($this['ad_profile_id']);
    }
}
