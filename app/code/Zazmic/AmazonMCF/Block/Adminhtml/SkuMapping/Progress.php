<?php
declare(strict_types=1);

/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */

namespace Zazmic\AmazonMCF\Block\Adminhtml\SkuMapping;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Zazmic\AmazonMCF\Model\ResourceModel\SkuMapping\CollectionFactory as SkuMappingFactory;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\App\RequestInterface;
use Zazmic\AmazonMCF\Helper\Data;

class Progress extends \Magento\Backend\Block\Template
{
    /**
     * Block template.
     *
     * @var string
     */
    protected $_template = 'skumapping/progress.phtml';
    /**
     * @var Context
     */
    protected $context;
    /**
     * @var SkuMappingFactory
     */
    protected $skuMappingFactory;
    /**
     * @var CollectionFactory
     */
    protected $productCollectionFactory;
    /**
     * @var Data
     */
    protected $helper;
    /**
     * Construct
     *
     * @param Context $context
     * @param SkuMappingFactory $skuMappingFactory
     * @param CollectionFactory $productCollectionFactory
     * @param RequestInterface $request
     * @param Data $helper
     * @param array $data
     */
    public function __construct(
        Context $context,
        SkuMappingFactory $skuMappingFactory,
        CollectionFactory $productCollectionFactory,
        RequestInterface $request,
        Data $helper,
        array $data = []
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->skuMappingFactory = $skuMappingFactory;
        $this->helper = $helper;
        $this->request = $request;
        parent::__construct($context, $data);
    }

    /**
     * Get Synced (Enabled)
     */
    public function getSynced()
    {
        $websiteId = $this->request->getParam('website');
        $count=0;
        $mapped= $this->skuMappingFactory->create()
            ->addFieldToFilter('seller_sku', ['neq' => null])
            ->addFieldToFilter('sync_status', ['eq' => '1']);
        if ($websiteId !='') {
            $mapped->addFieldToFilter('website', ['eq' =>$websiteId]);
        }
        if ($mapped) {
            $count=$mapped->count();
        }
        return $count;
    }
    /**
     * Get Not Synced (Disabled)
     */
    public function getNotSynced()
    {
        $websiteId = $this->request->getParam('website');
        $count=0;
        $mapped= $this->skuMappingFactory->create()
            ->addFieldToFilter('seller_sku', ['neq' => null])
            ->addFieldToFilter('sync_status', ['eq' => '0']);
        if ($websiteId !='') {
            $mapped->addFieldToFilter('website', ['eq' =>$websiteId]);
        }
        if ($mapped) {
            $count=$mapped->count();
        }
        return $count;
    }
    /**
     * Get Mapped
     */
    public function getMapped()
    {
        $websiteId = $this->request->getParam('website');
        $count=0;
        $mapped= $this->skuMappingFactory->create()
            ->addFieldToFilter('seller_sku', ['neq' => null])
            ->addFieldToFilter('status', ['eq' => '1']);
        if ($websiteId !='') {
            $mapped->addFieldToFilter('website', ['eq' =>$websiteId]);
        }
        if ($mapped) {
            $count=$mapped->count();
        }
        return $count;
    }
    /**
     * Get Not Mapped
     */
    public function getNotMapped()
    {
        $count=0;
        $websiteId = $this->request->getParam('website');
        $collection = $this->productCollectionFactory->create();
        if ($websiteId !='') {
            $collection->addWebsiteFilter([$websiteId]);
        }
        if ($collection) {
            $count=$collection->count() - $this->getMapped();
        }
        return $count;
    }
}
