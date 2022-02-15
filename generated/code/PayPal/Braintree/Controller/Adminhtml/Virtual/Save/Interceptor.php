<?php
namespace PayPal\Braintree\Controller\Adminhtml\Virtual\Save;

/**
 * Interceptor class for @see \PayPal\Braintree\Controller\Adminhtml\Virtual\Save
 */
class Interceptor extends \PayPal\Braintree\Controller\Adminhtml\Virtual\Save implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \PayPal\Braintree\Model\Adapter\BraintreeAdapter $braintreeAdapter, \PayPal\Braintree\Gateway\Request\ChannelDataBuilder $channelDataBuilder)
    {
        $this->___init();
        parent::__construct($context, $braintreeAdapter, $channelDataBuilder);
    }

    /**
     * {@inheritdoc}
     */
    public function execute() : \Magento\Framework\Controller\Result\Redirect
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
