<?php
declare(strict_types=1);

/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */

namespace Zazmic\AmazonMCF\Model\Shippingrate;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

class DataProvider extends AbstractDataProvider
{
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
        $settingList= [
             "standard_enable"  => 'zazmic/shipping_standard/standard_enable',
             "standard_rate"    => 'zazmic/shipping_standard/standard_rate',
             "standard_order"   => 'zazmic/shipping_standard/standard_order',
             "standard_desc"    => 'zazmic/shipping_standard/standard_desc',
             "expedited_enable" => 'zazmic/shipping_expedited/expedited_enable',
             "expedited_rate"   => 'zazmic/shipping_expedited/expedited_rate',
             "expedited_order"  => 'zazmic/shipping_expedited/expedited_order',
             "expedited_desc"   => 'zazmic/shipping_expedited/expedited_desc',
             "priority_enable"  => 'zazmic/shipping_priority/priority_enable',
             "priority_rate"    => 'zazmic/shipping_priority/priority_rate',
             "priority_order"   => 'zazmic/shipping_priority/priority_order',
             "priority_desc"    => 'zazmic/shipping_priority/priority_desc',
        ];
        return $settingList;
    }
}
