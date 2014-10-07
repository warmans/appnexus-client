<?php
namespace ANClient\Entity;

use ANClient\Entity;
use ANClient\Resource\AdProfileResource;
use ANClient\Resource\ProfileResource;

class AdQualityRule extends Entity
{
    /**
     * @return AdProfile
     */
    public function fetchAdProfile()
    {
        $adProfileRes = new AdProfileResource($this->resource->getClient());
        return $adProfileRes->fetchId($this['ad_profile_id']);
    }

    /**
     * Note that profiles are different from ad profiles.
     *
     * @throws \RuntimeException
     * @return Entity
     */
    public function fetchProfile()
    {
        //profile_id is optional so beware
        if (!$this['profile_id']) {
            throw new \RuntimeException('Entity has no profile ID');
        }

        $profileRes = new ProfileResource($this->resource->getClient());
        return $profileRes->fetchId($this['profile_id']);
    }
}
