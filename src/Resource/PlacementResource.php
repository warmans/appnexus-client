<?php
namespace ANClient\Resource;

/**
 * @link https://wiki.appnexus.com/display/api/Placement+Service
 * @package ANClient\Resource
 */
class PlacementResource extends AbstractResource
{
    /**
     * @return string
     */
    public function getIdName()
    {
        return 'placement_id';  //@fixme this is used for locating children - are they any? I don't think this is valid
    }

    /**
     * @return string
     */
    public function getSingularName()
    {
        return 'placement';
    }

    /**
     * @return string
     */
    public function getPluralName()
    {
        return 'placements';
    }
}
