<?php
declare(strict_types=1);

/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */

namespace Zazmic\AmazonMCF\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Zazmic\AmazonMCF\Api\SkuMappingRepositoryInterface;
use Zazmic\AmazonMCF\Model\ConfigManager;
use Zazmic\AmazonMCF\Api\Data\SkuMappingInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Message\ManagerInterface;
use Zazmic\AmazonMCF\Helper\Data;
use Magento\Framework\Stdlib\DateTime\DateTime;

class Productsaveafter implements ObserverInterface
{
    /**
     * @var SkuMappingRepositoryInterface
     */
    private $skuMappingRepository;
    /**
     * @var ConfigManager
     */
    private $configManager;
    /**
     * @var SkuMappingInterfaceFactory
     */
    private $skuMappingInterfaceFactory;
     /**
      * @var DataObjectHelper
      */
    private $dataObjectHelper;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var ManagerInterface
     */
    private $messageManager;
    /**
     * @var Data
     */
    private $helper;
     /**
      * @var DateTime
      */
    private $date;
    /**
     * @param SkuMappingRepositoryInterface $skuMappingRepository
     * @param ConfigManager $configManager
     * @param SkuMappingInterfaceFactory $skuMappingInterfaceFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param StoreManagerInterface $storeManager
     * @param ManagerInterface $messageManager
     * @param Data $helper
     * @param DateTime $date
     */
    public function __construct(
        SkuMappingRepositoryInterface $skuMappingRepository,
        ConfigManager $configManager,
        SkuMappingInterfaceFactory $skuMappingInterfaceFactory,
        DataObjectHelper $dataObjectHelper,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        StoreManagerInterface $storeManager,
        ManagerInterface $messageManager,
        Data $helper,
        DateTime $date
    ) {
        $this->skuMappingRepository = $skuMappingRepository;
        $this->configManager = $configManager;
        $this->skuMappingInterfaceFactory = $skuMappingInterfaceFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->storeManager = $storeManager;
        $this->messageManager = $messageManager;
        $this->helper = $helper;
        $this->date = $date;
    }
    /**
     * Execute
     *
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        $eventProduct = $observer->getEvent()->getProduct();
        $date = $this->date->gmtDate();
        $productId = $eventProduct->getId();
        $sku = $eventProduct->getSku();
        $mcf_mapStatus = $eventProduct->getCustomAttribute('mcf_enabled')->getValue();
        $activeWebsites = $eventProduct->getWebsiteIds();
        $skuId = '';
        $sellerSku = '';
        $asin = '';
        $website = '';
        $mappingProductId = '';
        $mappingProductSellerSku = '';
        $mappingProductSku = '';
        $mappingProduct = null;
        foreach ($activeWebsites as $activeWebsite) {
            if ($this->helper->isConnectedExist($activeWebsite, 'spapi_oauth_code')) {
                if ($productId) {
                    $skuSearchCriteria = $this->searchCriteriaBuilder
                    ->addFilter('product_id', $productId, 'eq')
                    ->addFilter('website', $activeWebsite, 'eq')->create();
                        $mappingProduct = $this->skuMappingRepository->getList($skuSearchCriteria);
                    foreach ($mappingProduct->getItems() as $mappingProductItem) {
                        $mappingProductId = $mappingProductItem['id'];
                        $mappingProductSellerSku =  $mappingProductItem['seller_sku'];
                        $mappingProductSku = $mappingProductItem['sku'];
                    }
                    if ($mcf_mapStatus == 0) {
                        if ($mappingProductId != '') {
                                $rawData = $this->skuMappingRepository->getById($mappingProductId);
                                $rawData->setSku($sku);
                                $rawData->setSellerSku('');
                                $rawData->setAsin('');
                                $rawData->setAmazonProductName('');
                                $rawData->setWebsite($activeWebsite);
                                $rawData->setStatus(0);
                                $rawData->setSyncStatus(0);
                                $rawData->setUpdatedAt($date);
                                $result=$this->skuMappingRepository->save($rawData);
                        }
                    } else {
                        $skuSearchCriteria = $this->searchCriteriaBuilder
                        ->addFilter('seller_sku', $sku, 'eq')
                        ->addFilter('website', $activeWebsite, 'eq')->create();
                            $skuData = $this->skuMappingRepository->getList($skuSearchCriteria);
                        foreach ($skuData->getItems() as $skuValue) {
                            $skuId = $skuValue['id'];
                        }
                        $response = $this->configManager->getMcfMapStatus('inventory', $sku, $activeWebsite);

                        if (isset($response['seller_sku']) && $skuId == '') {
                            $seller_sku = $response['seller_sku'];
                            $asin = $response['asin'];
                            $amcfProductName = $response['productName'];
                            $website = $activeWebsite;
                            $status = 1;
                            $syncStatus = 1;
                        } else {
                            $seller_sku = '';
                            $asin = '';
                            $amcfProductName = '';
                            $website = $activeWebsite;
                            $status = 0;
                            $syncStatus = 0;
                        }
                        if (!empty($mappingProduct->getItems()) && isset($mappingProductId)) {
                            $rawData = $this->skuMappingRepository->getById($mappingProductId);
                            if ($mappingProductSellerSku =='' || $skuId == '') {
                                $rawData->setSku($sku);
                                $rawData->setSellerSku($seller_sku);
                                $rawData->setAsin($asin);
                                $rawData->setAmazonProductName($amcfProductName);
                                $rawData->setWebsite($website);
                                $rawData->setStatus($status);
                                $rawData->setSyncStatus($syncStatus);
                            } else {
                                $rawData->setSku($sku);
                                $rawData->setStatus($mcf_mapStatus);
                                $rawData->setSyncStatus($mcf_mapStatus);
                                $rawData->setWebsite($website);
                            }
                            $result = $this->skuMappingRepository->save($rawData);
                            $this->configManager->updateProduct($productId, $status, $activeWebsite);
                            $this->configManager->updateProduct($productId, $status, 0);
                        } else {
                            $data = [
                                'product_id' => $productId,
                                'sku' => $sku,
                                'seller_sku' => $seller_sku,
                                'asin' => $asin,
                                'amazon_product_name' => $amcfProductName,
                                'status' => $status,
                                'sync_status' => $syncStatus,
                                'website' => $website
                            ];
                            $collection = $this->skuMappingInterfaceFactory->create();
                            $this->dataObjectHelper->populateWithArray(
                                $collection,
                                $data,
                                skuMappingInterface::class
                            );
                            if (isset($response['seller_sku'])) {
                                $result = $this->skuMappingRepository->save($collection);
                            }
                        
                            $this->configManager->updateProduct($productId, $status, $activeWebsite);
                            $this->configManager->updateProduct($productId, $status, 0);
                        }
                        if ($skuId && $mappingProductSku  != $sku || empty($skuId) && empty($response['seller_sku'])) {
                                $this->messageManager->addErrorMessage("Failed to map the product.
                                Either No seller sku found or the sku is already mapped to another product");
                        }
                    }
                }
            }
        }
        return $this;
    }
}
