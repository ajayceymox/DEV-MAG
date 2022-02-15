<?php
declare(strict_types=1);

/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */

namespace Zazmic\AmazonMCF\Block\Adminhtml\ShipmentStatus;

use Magento\Backend\Block\Template;
use Magento\Store\Model\StoreRepository;
use Magento\Backend\Block\Template\Context;
use Zazmic\AmazonMCF\Model\ConfigManager;
use Zazmic\AmazonMCF\Model\Config\Source\SyncStatus;
use Zazmic\AmazonMCF\Helper\Data;
use Zazmic\AmazonMCF\Model\Config\Source\ActiveMethods;
use Magento\Framework\Serialize\Serializer\Json;

class Tabs extends Template
{
    /**
     * @var $_template
     */
    protected $_template = 'shipment/shipment_status_tabs.phtml';

    /**
     * @var StoreRepository
     */
    protected $storeRepository;
    /**
     * @var Json
     */
    private $json;
    /**
     * @param Context $context
     * @param StoreRepository $storeRepository
     * @param ConfigManager $configManager
     * @param SyncStatus $syncStatus
     * @param Data $helper
     * @param ActiveMethods $activeMethods
     * @param array $data
     * @param Json $json
     */
    public function __construct(
        Context $context,
        StoreRepository $storeRepository,
        ConfigManager $configManager,
        SyncStatus $syncStatus,
        Data $helper,
        ActiveMethods $activeMethods,
        Json $json,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->storeRepository = $storeRepository;
        $this->configManager = $configManager;
        $this->syncStatus =  $syncStatus;
        $this->helper = $helper;
        $this->activeMethods = $activeMethods;
        $this->json = $json;
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
     * Get Active Shipping Methods
     *
     * @return array
     */
    public function getActiveMethods()
    {
        return $this->activeMethods->getOptionArray();
    }
    /**
     * Get Amazon Shipping Methods
     *
     * @return array
     */
    public function getAmazonMethods()
    {
        return [
            "Standard" => "Standard",
            "Expedited" => "Expedited",
            "Priority" => "Priority"
        ];
    }
    /**
     * Get Unseriliazed Data
     *
     * @param string $str 
     * @return array
     */
    public function getUnserialized($str)
    {
        try {
            return $this->json->unserialize($str);
        } catch (\Exception $e) {
            return null;
        }
    }
}
