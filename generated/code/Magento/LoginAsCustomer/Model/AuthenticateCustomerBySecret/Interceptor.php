<?php
namespace Magento\LoginAsCustomer\Model\AuthenticateCustomerBySecret;

/**
 * Interceptor class for @see \Magento\LoginAsCustomer\Model\AuthenticateCustomerBySecret
 */
class Interceptor extends \Magento\LoginAsCustomer\Model\AuthenticateCustomerBySecret implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\LoginAsCustomerApi\Api\GetAuthenticationDataBySecretInterface $getAuthenticationDataBySecret, \Magento\Customer\Model\Session $customerSession)
    {
        $this->___init();
        parent::__construct($getAuthenticationDataBySecret, $customerSession);
    }

    /**
     * {@inheritdoc}
     */
    public function execute(string $secret) : void
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'execute');
        if (!$pluginInfo) {
            parent::execute($secret);
        } else {
            $this->___callPlugins('execute', func_get_args(), $pluginInfo);
        }
    }
}
