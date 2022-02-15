<?php
namespace Zazmic\AmazonMCF\Controller\Adminhtml\SkuMapping\InlineEdit;

/**
 * Interceptor class for @see \Zazmic\AmazonMCF\Controller\Adminhtml\SkuMapping\InlineEdit
 */
class Interceptor extends \Zazmic\AmazonMCF\Controller\Adminhtml\SkuMapping\InlineEdit implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\Registry $coreRegistry, \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory, \Psr\Log\LoggerInterface $logger, \Magento\Framework\App\RequestInterface $request, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Framework\Api\DataObjectHelper $dataObjectHelper, \Magento\Framework\Controller\Result\RedirectFactory $redirectFactory, \Magento\Framework\Controller\Result\JsonFactory $jsonFactory, \Zazmic\AmazonMCF\Api\SkuMappingRepositoryInterface $skuMappingRepository, \Zazmic\AmazonMCF\Api\Data\SkuMappingInterfaceFactory $skuMappingInterfaceFactory, \Zazmic\AmazonMCF\Helper\Data $helper, \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder, \Zazmic\AmazonMCF\Model\ConfigManager $configManager, \Zazmic\AmazonMCF\Model\McfLogManager $mcfLogManager, \Magento\Framework\Stdlib\DateTime\DateTime $date)
    {
        $this->___init();
        parent::__construct($context, $coreRegistry, $resultLayoutFactory, $logger, $request, $resultPageFactory, $dataObjectHelper, $redirectFactory, $jsonFactory, $skuMappingRepository, $skuMappingInterfaceFactory, $helper, $searchCriteriaBuilder, $configManager, $mcfLogManager, $date);
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
