<?php
namespace ANClient\Resource;

/**
 * @link https://wiki.appnexus.com/display/api/Ad+Quality+Rule+Service
 * @package ANClient\Resource
 */
class AdQualityRuleResource extends AbstractResource
{
    /**
     * @return string
     */
    public function getIdName()
    {
        return 'ad_quality_rule_id'; //todo not confirmed
    }

    /**
     * @return string
     */
    public function getSingularName()
    {
        return 'ad-quality-rule';
    }

    /**
     * @return string
     */
    public function getPluralName()
    {
        return 'ad-quality-rules';
    }

    /**
     * @return string
     */
    public function getEntityClassName()
    {
        return '\\ANClient\\Entity\\AdQualityRule';
    }
}
