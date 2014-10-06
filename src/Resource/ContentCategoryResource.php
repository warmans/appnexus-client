<?php
namespace ANClient\Resource;

/**
 * @link https://wiki.appnexus.com/display/api/Site+Service
 * @package ANClient\Resource
 */
class ContentCategoryResource extends AbstractResource
{
    /**
     * @return string
     */
    public function getIdName()
    {
        return 'category_id';
    }

    /**
     * @return string
     */
    public function getSingularName()
    {
        return 'content-category';
    }

    /**
     * @return string
     */
    public function getPluralName()
    {
        return 'content-categories';
    }
}
