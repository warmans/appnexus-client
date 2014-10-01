<?php
namespace ANClient\Resource;

/**
 * @link https://wiki.appnexus.com/display/api/Ad+Profile+Service
 * @package ANClient\Resource
 */
class AdProfileResource extends AbstractResource
{
    /**
     * @return string
     */
    public function getIdName()
    {
        return 'ad_profile_id';
    }

    /**
     * @return string
     */
    public function getSingularName()
    {
        return 'ad-profile';
    }

    /**
     * @return string
     */
    public function getPluralName()
    {
        return 'ad-profiles';
    }

    /**
     * @return string
     */
    public function getEntityClassName()
    {
        return '\\ANClient\\Entity\\AdProfile';
    }
}
