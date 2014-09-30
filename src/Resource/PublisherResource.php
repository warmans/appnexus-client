<?php
namespace ANClient\Resource;

class PublisherResource extends AbstractResource
{
    /**
     * e.g. publisher_id
     *
     * @return string
     */
    public function getIdName()
    {
        return 'publisher_id';
    }

    /**
     * E.g. site, publisher
     *
     * @return string
     */
    public function getSingularName()
    {
        return 'publisher';
    }

    /**
     * E.g. sites, publishers
     *
     * @return string
     */
    public function getPluralName()
    {
        return 'publishers';
    }

    public function getEntityClassName()
    {
        return '\\ANClient\\Entity\\Publisher';
    }
}
