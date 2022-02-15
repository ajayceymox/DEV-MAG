<?php
declare(strict_types=1);

/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */

namespace Zazmic\AmazonMCF\Model\ShipmentStatus;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

class DataProvider extends AbstractDataProvider
{
    /**
     * @var CollectionFactory
     */
    private $requestFactory;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param StoreManagerInterface $storeManager
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        StoreManagerInterface $storeManager,
        array $meta = [],
        array $data = []
    ) {
        $this->collectionFactory    = $collectionFactory;
        $this->collection           = $this->collectionFactory->create();
        $this->storeManager         = $storeManager;
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
        $settingList = [
             "shipment_sync_status"  => 'zazmic/shipmentstatus/shipment_sync_status',
             "shipment_sync_interval" => 'zazmic/shipmentstatus/shipment_sync_interval'
        ];
        return $settingList;
    }
}
