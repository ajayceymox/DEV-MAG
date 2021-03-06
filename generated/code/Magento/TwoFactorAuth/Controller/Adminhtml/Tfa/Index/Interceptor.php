<?php
namespace Magento\TwoFactorAuth\Controller\Adminhtml\Tfa\Index;

/**
 * Interceptor class for @see \Magento\TwoFactorAuth\Controller\Adminhtml\Tfa\Index
 */
class Interceptor extends \Magento\TwoFactorAuth\Controller\Adminhtml\Tfa\Index implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\TwoFactorAuth\Api\TfaSessionInterface $session, \Magento\TwoFactorAuth\Api\UserConfigManagerInterface $userConfigManager, \Magento\TwoFactorAuth\Api\TfaInterface $tfa, \Magento\TwoFactorAuth\Api\UserConfigRequestManagerInterface $userConfigRequestManager, \Magento\Authorization\Model\UserContextInterface $userContext)
    {
        $this->___init();
        parent::__construct($context, $session, $userConfigManager, $tfa, $userConfigRequestManager, $userContext);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'execute');
        if (!$pluginInfo) {
            return parent::execute();
        } else {
            return $this->___callPlugins('execute', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'dispatch');
        if (!$pluginInfo) {
            return parent::dispatch($request);
        } else {
            return $this->___callPlugins('dispatch', func_get_args(), $pluginInfo);
        }
    }
}
