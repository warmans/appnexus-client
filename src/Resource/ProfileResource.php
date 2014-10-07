<?php
namespace ANClient\Resource;

/**
 * @link https://wiki.appnexus.com/display/api/Profile+Service
 * @package ANClient\Resource
 */
class ProfileResource extends AbstractResource
{
    /**
     * @return string
     */
    public function getIdName()
    {
        return 'profile_id';
    }

    /**
     * @return string
     */
    public function getSingularName()
    {
        return 'profile';
    }

    /**
     * @return string
     */
    public function getPluralName()
    {
        return 'profiles';
    }
}
