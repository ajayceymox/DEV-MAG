<?php
namespace Zazmic\AmazonMCF\Controller\Adminhtml\Log\MassDelete;

/**
 * Interceptor class for @see \Zazmic\AmazonMCF\Controller\Adminhtml\Log\MassDelete
 */
class Interceptor extends \Zazmic\AmazonMCF\Controller\Adminhtml\Log\MassDelete implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\Registry $coreRegistry, \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory, \Psr\Log\LoggerInterface $logger, \Zazmic\AmazonMCF\Model\ConfigManager $configManager, \Magento\Ui\Component\MassAction\Filter $filter, \Zazmic\AmazonMCF\Model\ResourceModel\McfLog\CollectionFactory $collectionFactory, \Zazmic\AmazonMCF\Api\McfLogRepositoryInterface $mcfLogRepository, \Zazmic\AmazonMCF\Api\Data\McfLogInterfaceFactory $mcfLogInterfaceFactory, \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder)
    {
        $this->___init();
        parent::__construct($context, $coreRegistry, $resultLayoutFactory, $logger, $configManager, $filter, $collectionFactory, $mcfLogRepository, $mcfLogInterfaceFactory, $searchCriteriaBuilder);
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
