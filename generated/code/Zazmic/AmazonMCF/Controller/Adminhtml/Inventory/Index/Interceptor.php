<?php
namespace Zazmic\AmazonMCF\Controller\Adminhtml\Inventory\Index;

/**
 * Interceptor class for @see \Zazmic\AmazonMCF\Controller\Adminhtml\Inventory\Index
 */
class Interceptor extends \Zazmic\AmazonMCF\Controller\Adminhtml\Inventory\Index implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\Registry $coreRegistry, \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory, \Psr\Log\LoggerInterface $logger, \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Zazmic\AmazonMCF\Model\ConfigManager $configManager)
    {
        $this->___init();
        parent::__construct($context, $coreRegistry, $resultLayoutFactory, $logger, $cacheTypeList, $resultPageFactory, $configManager);
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
