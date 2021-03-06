<?php
namespace Yotpo\Yotpo\Controller\Adminhtml\External\Analytics;

/**
 * Interceptor class for @see \Yotpo\Yotpo\Controller\Adminhtml\External\Analytics
 */
class Interceptor extends \Yotpo\Yotpo\Controller\Adminhtml\External\Analytics implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Yotpo\Yotpo\Model\Config $yotpoConfig)
    {
        $this->___init();
        parent::__construct($context, $yotpoConfig);
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
