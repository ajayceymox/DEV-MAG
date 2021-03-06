<?php
namespace Magento\EncryptionKey\Controller\Adminhtml\Crypt\Key\Index;

/**
 * Interceptor class for @see \Magento\EncryptionKey\Controller\Adminhtml\Crypt\Key\Index
 */
class Interceptor extends \Magento\EncryptionKey\Controller\Adminhtml\Crypt\Key\Index implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\App\DeploymentConfig\Writer $writer)
    {
        $this->___init();
        parent::__construct($context, $writer);
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
