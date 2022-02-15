<?php
namespace Magento\User\Model\UserValidationRules;

/**
 * Interceptor class for @see \Magento\User\Model\UserValidationRules
 */
class Interceptor extends \Magento\User\Model\UserValidationRules implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct()
    {
        $this->___init();
    }

    /**
     * {@inheritdoc}
     */
    public function addUserInfoRules(\Magento\Framework\Validator\DataObject $validator)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'addUserInfoRules');
        if (!$pluginInfo) {
            return parent::addUserInfoRules($validator);
        } else {
            return $this->___callPlugins('addUserInfoRules', func_get_args(), $pluginInfo);
        }
    }
}
