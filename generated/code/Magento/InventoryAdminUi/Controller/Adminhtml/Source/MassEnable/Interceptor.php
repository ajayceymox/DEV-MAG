<?php
namespace Magento\InventoryAdminUi\Controller\Adminhtml\Source\MassEnable;

/**
 * Interceptor class for @see \Magento\InventoryAdminUi\Controller\Adminhtml\Source\MassEnable
 */
class Interceptor extends \Magento\InventoryAdminUi\Controller\Adminhtml\Source\MassEnable implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\InventoryCatalogApi\Api\DefaultSourceProviderInterface $defaultSourceProvider, \Magento\Inventory\Model\ResourceModel\Source\CollectionFactory $sourceCollectionFactory, \Magento\Backend\App\Action\Context $context, \Magento\Ui\Component\MassAction\Filter $massActionFilter, \Magento\InventoryApi\Api\Data\SourceInterfaceFactory $sourceFactory, \Magento\InventoryApi\Api\SourceRepositoryInterface $sourceRepository)
    {
        $this->___init();
        parent::__construct($defaultSourceProvider, $sourceCollectionFactory, $context, $massActionFilter, $sourceFactory, $sourceRepository);
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
