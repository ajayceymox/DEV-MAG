<?php
namespace Magento\InventoryIndexer\Indexer\Stock\Strategy\Sync;

/**
 * Interceptor class for @see \Magento\InventoryIndexer\Indexer\Stock\Strategy\Sync
 */
class Interceptor extends \Magento\InventoryIndexer\Indexer\Stock\Strategy\Sync implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\InventoryIndexer\Indexer\Stock\GetAllStockIds $getAllStockIds, \Magento\InventoryMultiDimensionalIndexerApi\Model\IndexStructureInterface $indexStructureHandler, \Magento\InventoryMultiDimensionalIndexerApi\Model\IndexHandlerInterface $indexHandler, \Magento\InventoryMultiDimensionalIndexerApi\Model\IndexNameBuilder $indexNameBuilder, \Magento\InventoryIndexer\Indexer\Stock\IndexDataProviderByStockId $indexDataProviderByStockId, \Magento\InventoryMultiDimensionalIndexerApi\Model\IndexTableSwitcherInterface $indexTableSwitcher, \Magento\InventoryCatalogApi\Api\DefaultStockProviderInterface $defaultStockProvider)
    {
        $this->___init();
        parent::__construct($getAllStockIds, $indexStructureHandler, $indexHandler, $indexNameBuilder, $indexDataProviderByStockId, $indexTableSwitcher, $defaultStockProvider);
    }

    /**
     * {@inheritdoc}
     */
    public function executeFull() : void
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'executeFull');
        if (!$pluginInfo) {
            parent::executeFull();
        } else {
            $this->___callPlugins('executeFull', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function executeList(array $stockIds) : void
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'executeList');
        if (!$pluginInfo) {
            parent::executeList($stockIds);
        } else {
            $this->___callPlugins('executeList', func_get_args(), $pluginInfo);
        }
    }
}
