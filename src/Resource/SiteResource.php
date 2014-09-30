<?php
namespace ANClient\Resource;

class SiteResource extends AbstractResource
{
    /**
     * @return string
     */
    public function getIdName()
    {
        return 'placement_id';  //wtf
    }

    /**
     * @return string
     */
    public function getSingularName()
    {
        return 'site';
    }

    /**
     * @return string
     */
    public function getPluralName()
    {
        return 'sites';
    }
}
