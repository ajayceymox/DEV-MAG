<?php
namespace Zazmic\AmazonMCF\Controller\Adminhtml\SkuMapping\Import;

/**
 * Interceptor class for @see \Zazmic\AmazonMCF\Controller\Adminhtml\SkuMapping\Import
 */
class Interceptor extends \Zazmic\AmazonMCF\Controller\Adminhtml\SkuMapping\Import implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\Registry $coreRegistry, \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory, \Psr\Log\LoggerInterface $logger, \Zazmic\AmazonMCF\Model\ConfigManager $configManager, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Framework\App\RequestInterface $request, \Magento\Framework\Filesystem $fileSystem, \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory, \Magento\Framework\File\Csv $csvProcessor, \Magento\Framework\Message\ManagerInterface $messageManager, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Framework\Filesystem\Driver\File $file, \Magento\Catalog\Api\ProductRepositoryInterface $productRepository, \Zazmic\AmazonMCF\Model\McfLogManager $mcfLogManager, \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder, \Magento\Framework\MessageQueue\PublisherInterface $publisher, \Magento\Framework\Serialize\Serializer\Json $json)
    {
        $this->___init();
        parent::__construct($context, $coreRegistry, $resultLayoutFactory, $logger, $configManager, $resultPageFactory, $request, $fileSystem, $uploaderFactory, $csvProcessor, $messageManager, $storeManager, $file, $productRepository, $mcfLogManager, $searchCriteriaBuilder, $publisher, $json);
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
