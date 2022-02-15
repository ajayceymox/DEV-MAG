<?php
namespace Magento\LoginAsCustomerAdminUi\Controller\Adminhtml\Login\Login;

/**
 * Interceptor class for @see \Magento\LoginAsCustomerAdminUi\Controller\Adminhtml\Login\Login
 */
class Interceptor extends \Magento\LoginAsCustomerAdminUi\Controller\Adminhtml\Login\Login implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Backend\Model\Auth\Session $authSession, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository, \Magento\LoginAsCustomerApi\Api\ConfigInterface $config, \Magento\LoginAsCustomerApi\Api\Data\AuthenticationDataInterfaceFactory $authenticationDataFactory, \Magento\LoginAsCustomerApi\Api\SaveAuthenticationDataInterface $saveAuthenticationData, \Magento\LoginAsCustomerApi\Api\DeleteAuthenticationDataForUserInterface $deleteAuthenticationDataForUser, \Magento\Framework\Url $url)
    {
        $this->___init();
        parent::__construct($context, $authSession, $storeManager, $customerRepository, $config, $authenticationDataFactory, $saveAuthenticationData, $deleteAuthenticationDataForUser, $url);
    }

    /**
     * {@inheritdoc}
     */
    public function execute() : \Magento\Framework\Controller\ResultInterface
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
