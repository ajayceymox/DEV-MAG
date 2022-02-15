<?php declare(strict_types=1);
/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */
namespace Zazmic\AmazonMCF\Plugin;

use Magento\Checkout\Model\Session ;
use Zazmic\AmazonMCF\Api\SkuMappingRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Checkout\Model\Cart;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Zazmic\AmazonMCF\Model\ConfigManager;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Serialize\Serializer\Json;

class ApplyShipping
{

    /**
     * @var Cart
     */
    protected $cart;
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;
    /**
     * @var SkuMappingRepositoryInterface
     */
    private $skuMappingRepository;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var ConfigManager
     */
    private $configManager;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var Json
     */
    private $json;

    /**
     * @param Cart $cart
     * @param ProductRepositoryInterface $productRepository
     * @param SkuMappingRepositoryInterface $skuMappingRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param ConfigManager $configManager
     * @param StoreManagerInterface $storeManager
     * @param Json $json
     */
    public function __construct(
        Cart $cart,
        ProductRepositoryInterface $productRepository,
        SkuMappingRepositoryInterface $skuMappingRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ConfigManager $configManager,
        StoreManagerInterface $storeManager,
        Json $json
    ) {
        $this->cart = $cart;
        $this->productRepository = $productRepository;
        $this->skuMappingRepository = $skuMappingRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->configManager = $configManager;
        $this->storeManager = $storeManager;
        $this->json = $json;
    }
     /**
      * Collect rates of given carrier
      *
      * @param \Magento\Shipping\Model\Shipping $subject
      * @param \Closure $proceed
      * @param string $carrierCode
      * @param RateRequest $request
      *
      * @return $this
      */
    public function aroundCollectCarrierRates(
        \Magento\Shipping\Model\Shipping $subject,
        \Closure $proceed,
        $carrierCode,
        $request
    ) {
        if ($this->configManager->isConnected() && $this->configManager->getConfig('shipping_config_active')) {
            $quoteItemStatus = $this->getQuoteItemsStatus();
            $amazonShippingStatus = $quoteItemStatus['amazon-mcf-status'];
            if ($this->configManager->getConfig('shipping_config_exclusive')) {
                if ($amazonShippingStatus == 2 && $this->configManager->getConfig('shipping_config_ship')) {
                    $defaultShipping = $this->configManager->getConfig('shipping_config_default_method');
                    $defaultShipping=$this->json->unserialize($defaultShipping);
                    $defaultShipping = array_keys($defaultShipping);
                    foreach ($defaultShipping as $shipMethod) {
                        $carrierCodes = explode('_', $shipMethod);
                        if (isset($carrierCodes[0]) && $carrierCode == $carrierCodes[0]) {
                            return $proceed($carrierCode, $request);
                        }
                    }
                } elseif ($amazonShippingStatus == 1 && $carrierCode =='amazonshipping') {
                    return $proceed($carrierCode, $request);
                } elseif ($amazonShippingStatus == 0 && $carrierCode !='amazonshipping') {
                    return $proceed($carrierCode, $request);
                } elseif ($amazonShippingStatus == 2 && $this->configManager->getConfig('shipping_config_ship')==0
                    && $carrierCode !='amazonshipping') {
                    return $proceed($carrierCode, $request);
                }
            } else {
                if ($amazonShippingStatus == 1) {
                    return $proceed($carrierCode, $request);
                } else {
                    if ($carrierCode !='amazonshipping') {
                        return $proceed($carrierCode, $request);
                    }
                }
            }
        } else {
            if ($carrierCode != 'amazonshipping') {
                return $proceed($carrierCode, $request);
            }
        }
    }

    /**
     * Get quote object associated with cart. By default it is current customer session quote
     *
     * @return \Magento\Quote\Model\Quote
     */
    public function getQuoteItemsStatus()
    {
        $website = $this->storeManager->getStore()->getWebsiteId();
        $quoteItemStatus = [];
        $mcfStatus = [];
        $quoteItems =  $this->cart->getQuote()->getAllItems();
        foreach ($quoteItems as $item) {
            $productId = $item->getProductId();
            $quoteItemId = $item->getId();
            $skuSearchCriteria = $this->searchCriteriaBuilder
            ->addFilter('product_id', $productId, 'eq')
            ->addFilter('website', $website, 'eq')->create();
            $skuMappingData = $this->skuMappingRepository->getList($skuSearchCriteria);
            if ($skuMappingData->getTotalCount() > 0) {
                foreach ($skuMappingData->getItems() as $data) {
                    if ($data->getStatus() && $data->getSyncStatus()) {
                        $quoteItemStatus['items'][] = [ "quote_item_id" => $quoteItemId,
                                                        "product_id" => $productId,
                                                        "mcf" =>1
                                                    ];
                        $mcfStatus[] = 1;
                    } else {
                        $quoteItemStatus['items'][] = [ "quote_item_id" => $quoteItemId,
                                                        "product_id" => $productId,
                                                        "mcf" => 0
                                                    ];
                        $mcfStatus[] = 0;
                    }
                }
            } else {
                $mcfStatus[] = 0;
                $quoteItemStatus['items'][] = [ "quote_item_id" => $quoteItemId,
                                                "product_id" => $productId,
                                                "mcf" => 0
                                            ];
            }
        }
        $mcf = array_unique($mcfStatus);
        if (count($mcf) > 1) {
            $amazonMcfStatus = 2;
        } else {
            $amazonMcfStatus = $mcf[0];
        }
        $quoteItemStatus['amazon-mcf-status'] = $amazonMcfStatus;
        return $quoteItemStatus;
    }
}
