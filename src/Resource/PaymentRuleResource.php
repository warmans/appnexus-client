<?php
namespace ANClient\Resource;

/**
 * @link https://wiki.appnexus.com/display/api/Payment+Rule+Service
 * @package ANClient\Resource
 */
class PaymentRuleResource extends AbstractResource
{
    /**
     * @return string
     */
    public function getIdName()
    {
        return 'payment_rule_id'; //?
    }

    /**
     * @return string
     */
    public function getSingularName()
    {
        return 'payment-rule';
    }

    /**
     * @return string
     */
    public function getPluralName()
    {
        return 'payment-rules';
    }
}
