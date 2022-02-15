<?php
declare(strict_types=1);

/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */

namespace Zazmic\AmazonMCF\Block\Adminhtml\ShippingRate;

use Magento\Backend\Block\Template;
use Magento\Store\Model\StoreRepository;
use Magento\Backend\Block\Template\Context;
use Zazmic\AmazonMCF\Model\ConfigManager;
use Zazmic\AmazonMCF\Model\Config\Source\SyncStatus;
use Zazmic\AmazonMCF\Helper\Data;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Directory\Model\CurrencyFactory;

class Tabs extends Template
{
    /**
     * @var $_template
     */
    protected $_template = 'shipment/shipment_rates_tabs.phtml';

    /**
     * @var StoreRepository
     */
    protected $storeRepository;
    /**
     * @param Context $context
     * @param StoreRepository $storeRepository
     * @param ConfigManager $configManager
     * @param SyncStatus $syncStatus
     * @param Data $helper
     * @param StoreManagerInterface $storeManager
     * @param CurrencyFactory $currencyFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        StoreRepository $storeRepository,
        ConfigManager $configManager,
        SyncStatus $syncStatus,
        Data $helper,
        StoreManagerInterface $storeManager,
        CurrencyFactory $currencyFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->storeRepository = $storeRepository;
        $this->configManager = $configManager;
        $this->syncStatus =  $syncStatus;
        $this->helper = $helper;
        $this->storeManager = $storeManager;
        $this->currency = $currencyFactory->create();
    }
    /**
     * Return array of active websites
     */
    public function getWebsites()
    {
        return $this->helper->getActiveConnections();
    }
    /**
     * Return config value
     *
     * @param string $path
     * @param int $scopeId
     * @return mixed
     */
    public function getConfigData($path, $scopeId)
    {
        return $this->configManager->getConfigFromScope($path, $scopeId);
    }
     /**
      * Return array of Sync intervel
      *
      * @return array
      */
    public function getOptions()
    {
        return $this->syncStatus->getOptionArray();
    }
    /**
     * Get country code
     *
     * @return string
     */
    protected function getCountry()
    {
        return $this->configManager->getConfigCountry();
    }
    /**
     * Get config path
     *
     * @param string $path
     * @return string
     */
    public function getConfigPath($path)
    {
        if ($path == 'shipment_sync_status') {
            return 'zazmic/shipmentstatus/shipment_sync_status';
        } elseif ($path == 'shipment_sync_interval') {
            return 'zazmic/shipmentstatus/shipment_sync_interval';
        }
        return "";
    }
    /**
     * Return array of shipping rate options
     *
     * @return array
     */
    public function getRateOptions()
    {
        $rates = [['Standard (3-5 days)','standard'],
                  ['Expedited (2 days)','expedited'],
                  ['Priority (1 day)','priority']];
        return $rates;
    }
    /**
     * Get currency symbol by website id
     *
     * @param int $websiteId
     * @return string
     */
    public function getCurrencySymbol(int $websiteId)
    {
        $storeId = $this->storeManager->getWebsite($websiteId)->getDefaultStore()->getId();
        $currencyCode = $this->storeManager->getStore($storeId)->getDefaultCurrencyCode();
        return $this->currency->load($currencyCode)->getCurrencySymbol();
    }
}
