<?php declare(strict_types=1);
/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */
namespace Zazmic\AmazonMCF\Model\ResourceModel\SkuMapping\Grid;

use Magento\Framework\Api\Search\SearchResultInterface as SearchInterface;
use Magento\Framework\Search\AggregationInterface;
use Zazmic\AmazonMCF\Model\ResourceModel\SkuMapping\Collection as GirdCollection;
use Magento\Framework\View\Element\UiComponent\DataProvider\Document;
use Magento\Framework\App\RequestInterface as Request;

class Collection extends GirdCollection implements SearchInterface
{
    /**
     * @var AggregationInterface
     */
    protected $aggregations;
    /**
     * @var Request
     */
    private $request;
 
    /**
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param Request $request
     * @param string $mainTable
     * @param string $eventPrefix
     * @param string $eventObject
     * @param string $resourceModel
     * @param string $model
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|string|null $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        Request $request,
        $mainTable,
        $eventPrefix,
        $eventObject,
        $resourceModel,
        $model = Document::class,
        \Magento\Framework\DB\Adapter\AdapterInterface  $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->request = $request;
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $connection,
            $resource
        );
        $this->_eventPrefix = $eventPrefix;
        $this->_eventObject = $eventObject;
        $this->_init($model, $resourceModel);
        $this->setMainTable($mainTable);
    }

    /**
     * GetAggregations
     *
     * @return AggregationInterface
     */
    public function getAggregations()
    {
        return $this->aggregations;
    }
 
    /**
     * SetAggregations
     *
     * @param AggregationInterface $aggregations
     * @return $this
     */
    public function setAggregations($aggregations)
    {
        $this->aggregations = $aggregations;
    }
 
    /**
     * Get search criteria.
     *
     * @return \Magento\Framework\Api\SearchCriteriaInterface|null
     */
    public function getSearchCriteria()
    {
        return null;
    }
 
    /**
     * Set search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setSearchCriteria(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null)
    {
        return $this;
    }
 
    /**
     * Get total count.
     *
     * @return int
     */
    public function getTotalCount()
    {
        return $this->getSize();
    }
 
    /**
     * Set total count.
     *
     * @param int $totalCount
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setTotalCount($totalCount)
    {
        return $this;
    }
 
    /**
     * Set items list.
     *
     * @param \Magento\Framework\Api\ExtensibleDataInterface[] $items
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setItems(array $items = null)
    {
        return $this;
    }
    /**
     * InitSelect.
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $joinCondition = '';
        if ($this->request->getParam('website')) {
            $joinCondition = 'and msm.website = '.$this->request->getParam('website');

        }
        $catalogProductEntityInt = $this->getTable('catalog_product_entity_int');
        $eavAttribute = $this->getTable('eav_attribute');
        $this->getSelect()
        ->joinLeft(
            $eavAttribute.' as ea',
            'ea.attribute_code = "mcf_enabled"',
            [
            'attribute_id' => 'ea.attribute_id',
            ]
        )
        ->joinLeft('catalog_product_website as cpw', 'main_table.entity_id= cpw.product_id', [
            'website_id' => 'website_id'
        ])
        ->joinLeft(
            $catalogProductEntityInt.' as cpe',
            'main_table.entity_id = cpe.entity_id AND cpe.attribute_id = ea.attribute_id 
            and cpe.store_id = cpw.website_id',
            [
            'mcf_enabled' => 'cpe.value',
            ]
        )
        ->joinLeft('mcf_sku_mapping as msm', 'main_table.entity_id=msm.product_id '. $joinCondition, [
            'product_id' => 'product_id',
            'amazon_product_name' => 'amazon_product_name',
            'status' => 'msm.status',
            'sync_status' => 'sync_status',
             'website' => 'msm.website',
            'seller_sku' => 'seller_sku',
        ])
        ->group('cpw.product_id');
        $this->addFilterToMap("entity_id", "main_table.entity_id");
        $this->addFilterToMap("sku", "main_table.sku");
        $this->addFilterToMap("amazon_product_name", "amazon_product_name");
        $this->addFilterToMap("sync_status", "msm.sync_status");
        $this->addFilterToMap("seller_sku", "msm.seller_sku");
        $this->addFilterToMap("mcf_enabled", "cpe.value");
        $this->addFilterToMap("website", "cpw.website_id");
        return $this;
    }
}
