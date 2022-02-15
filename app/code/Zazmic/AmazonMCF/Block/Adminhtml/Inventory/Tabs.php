<?php
declare(strict_types=1);

/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */

namespace Zazmic\AmazonMCF\Block\Adminhtml\Inventory;

use Magento\Backend\Block\Template;
use Magento\Store\Model\StoreRepository;
use Magento\Backend\Block\Template\Context;
use Zazmic\AmazonMCF\Model\ConfigManager;
use Zazmic\AmazonMCF\Model\Config\Source\SyncStatus;
use Zazmic\AmazonMCF\Helper\Data;

class Tabs extends Template
{
    /**
     * @var $_template
     */
    protected $_template = 'Inventory/inventory_tabs.phtml';

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
     * @param array $data
     */
    public function __construct(
        Context $context,
        StoreRepository $storeRepository,
        ConfigManager $configManager,
        SyncStatus $syncStatus,
        Data $helper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->storeRepository = $storeRepository;
        $this->configManager = $configManager;
        $this->syncStatus =  $syncStatus;
        $this->helper = $helper;
    }
    /**
     * Return array of active websites
     */
    public function getWebsites()
    {
        return $this->helper->getWebsites();
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
      * Get country name based on scope
      *
      * @param int $scopeId
      * @return string
      */
    public function getConfigCountryName($scopeId)
    {
        $countryCode = $this->getConfigData('general_country', $scopeId);
        return $this->configManager->getConfigCountryName($countryCode);
    }
}
