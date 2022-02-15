<?php
declare(strict_types=1);

/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */

namespace Zazmic\AmazonMCF\Model\Inventory;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Zazmic\AmazonMCF\Model\Config\Source\SyncStatus;

class DataProvider extends AbstractDataProvider
{
    /**
     * @var CollectionFactory
     */
    private $requestFactory;
    /**
     * @var Status
     */
    public $status;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param Status $status
     * @param StoreManagerInterface $storeManager
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        SyncStatus $status,
        StoreManagerInterface $storeManager,
        array $meta = [],
        array $data = []
    ) {
        $this->collectionFactory    = $collectionFactory;
        $this->collection           = $this->collectionFactory->create();
        $this->storeManager         = $storeManager;
        $this->status               = $status;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        return [];
    }
    /**
     * Get Config Settings Paths
     *
     * @return  array
     */
    public function getConfigPath()
    {
        $settingList= [
             "sync_interval"  =>'zazmic/inventory_config/sync_interval',
             "auto_inventory"    =>'zazmic/inventory_config/auto_inventory',
        ];
        return $settingList;
    }
}
