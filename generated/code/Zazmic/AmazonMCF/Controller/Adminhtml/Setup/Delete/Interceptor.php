<?php
namespace Zazmic\AmazonMCF\Controller\Adminhtml\Setup\Delete;

/**
 * Interceptor class for @see \Zazmic\AmazonMCF\Controller\Adminhtml\Setup\Delete
 */
class Interceptor extends \Zazmic\AmazonMCF\Controller\Adminhtml\Setup\Delete implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\App\RequestInterface $request, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Framework\Controller\Result\RedirectFactory $redirectFactory, \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList, \Zazmic\AmazonMCF\Model\McfStoreManager $mcfStoreManager, \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder, \Zazmic\AmazonMCF\Api\SkuMappingRepositoryInterface $skuMappingRepository, \Zazmic\AmazonMCF\Model\ConfigManager $configManager, \Zazmic\AmazonMCF\Model\McfLogManager $mcfLogManager)
    {
        $this->___init();
        parent::__construct($context, $request, $resultPageFactory, $redirectFactory, $cacheTypeList, $mcfStoreManager, $searchCriteriaBuilder, $skuMappingRepository, $configManager, $mcfLogManager);
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
