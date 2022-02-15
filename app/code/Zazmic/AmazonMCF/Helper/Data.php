<?php declare (strict_types = 1);
/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */
namespace Zazmic\AmazonMCF\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\StoreRepository;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Directory\Api\CountryInformationAcquirerInterface;

class Data extends AbstractHelper
{
 
    private const MID_URL    = "https://ceymox.xyz/midmcf/";
    private const APPLICATION_ID = 'amzn1.sp.solution.de7d46ce-825e-490c-8389-ac49f62be9bf';

   /**
    * @var productRepository
    */
    private $productRepository;

    /**
     * @var StoreRepository
     */
    private $storeRepository;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param Context $context
     * @param ProductRepository $productRepository
     * @param StoreRepository $storeRepository
     * @param CountryInformationAcquirerInterface $countryInformationInterface
     * @param array $data
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Context $context,
        ProductRepository $productRepository,
        StoreRepository $storeRepository,
        CountryInformationAcquirerInterface $countryInformationInterface,
        array $data = []
    ) {
        $this->productRepository = $productRepository;
        $this->storeRepository = $storeRepository;
        $this->scopeConfig = $scopeConfig;
        $this->countryInformationInterface = $countryInformationInterface;
        parent::__construct($context);
    }

    /**
     * MCF allowed Countries
     *
     * @return  array
     */
    public function getAmcfCountries()
    {
        $amcfCountyList= [
                ['value' => '', 'label' => __('Select a country')],
                ['value' => 'CA', 'label' => __('Canada')],
                ['value' => 'MX', 'label' => __('Mexico')],
                ['value' => 'GB', 'label' => __('United Kingdom')],
                ['value' => 'BR', 'label' => __('Brazil')],
                ['value' => 'US', 'label' => __('United States of America')],
                ['value' => 'ES', 'label' => __('Spain')],
                ['value' => 'FR', 'label' => __('France')],
                ['value' => 'NL', 'label' => __('Netherlands')],
                ['value' => 'DE', 'label' => __('Germany')],
                ['value' => 'IT', 'label' => __('Italy')],
                ['value' => 'SE', 'label' => __('Sweden')],
                ['value' => 'PL', 'label' => __('Poland')],
                ['value' => 'EG', 'label' => __('Egypt')],
                ['value' => 'TR', 'label' => __('Turkey')],
                ['value' => 'SA', 'label' => __('Saudi Arabia')],
                ['value' => 'AE', 'label' => __('United Arab Emirates')],
                ['value' => 'IN', 'label' => __('India')],
                ['value' => 'SG', 'label' => __('Singapore')],
                ['value' => 'AU', 'label' => __('Australia')],
                ['value' => 'JP', 'label' => __('Japan')]
        ];
        return $amcfCountyList;
    }

    /**
     * MCF Versions
     *
     * @return  array
     */
    public function getAmcfVersions()
    {
        $versionList= [
                ['value' => '', 'label' => __('Select a version')],
                ['value' => 'beta', 'label' => __('Beta')],
                ['value' => 'stable', 'label' => __('Stable')],
        ];
        return $versionList;
    }

    /**
     * Connect config values
     *
     * @return  array
     */
    public function getConfigPathVariables()
    {
        $versionList= [
                "app_id"                         => "zazmic/amcf_general/app_id",
                "country"                        => "zazmic/amcf_general/country",
                "version"                        => "zazmic/amcf_general/version",
                "spapi_oauth_code"               => "zazmic/amcf_general/spapi_oauth_code",
                "state"                          => "zazmic/amcf_general/state",
                "selling_partner_id"             => "zazmic/amcf_general/selling_partner_id",
                "token"                          => "zazmic/amcf_general/token",
                "status"                         => "zazmic/amcf_general/status",
                "shipping_config_active"         => "carriers/amazonshipping/active",
                "shipping_config_title"          => "carriers/amazonshipping/title",
                "shipping_config_ship"           => "carriers/amazonshipping/ship",
                "shipping_config_exclusive"      => "carriers/amazonshipping/exclusive",
                "shipping_config_default_method" => "carriers/amazonshipping/default_method",
                "shipping_config_default_amazon_method" => "carriers/amazonshipping/default_amazon_method",
                "standard_enable"                => "zazmic/shipping_standard/standard_enable",
                "standard_rate"                  => "zazmic/shipping_standard/standard_rate",
                "standard_order"                 => "zazmic/shipping_standard/standard_order",
                "standard_desc"                  => "zazmic/shipping_standard/standard_desc",
                "expedited_enable"               => "zazmic/shipping_expedited/expedited_enable",
                "expedited_rate"                 => "zazmic/shipping_expedited/expedited_rate",
                "expedited_order"                => "zazmic/shipping_expedited/expedited_order",
                "expedited_desc"                 => "zazmic/shipping_expedited/expedited_desc",
                "priority_enable"                => "zazmic/shipping_priority/priority_enable",
                "priority_rate"                  => "zazmic/shipping_priority/priority_rate",
                "priority_order"                 => "zazmic/shipping_priority/priority_order",
                "priority_desc"                  => "zazmic/shipping_priority/priority_desc",
                "general_country"                => "general/country/default",
                "shipment_sync_status"           => "zazmic/shipmentstatus/shipment_sync_status",
                "shipment_sync_interval"         => "zazmic/shipmentstatus/shipment_sync_interval",
                "auto_inventory"                 => "zazmic/inventory_config/auto_inventory",
                "inventory_sync_interval"        => "zazmic/inventory_config/sync_interval",
                "base_url"                       => "web/unsecure/base_url"
        ];
        return $versionList;
    }

    /**
     * Get Product
     *
     * @param   int $id
     * @return  string
     */
    public function getProductById($id)
    {
        return $this->productRepository->getById($id);
    }

    /**
     * Return array of active websites
     */
    public function getWebsites()
    {
        $stores = $this->storeRepository->getList();
        $websites = [];
        foreach ($stores as $store) {
            if ($store['website_id'] != 0 && $store['is_active']) {
                 $websites[$store['website_id']] = $store["name"];
            }
        }
        return $websites;
    }
    /**
     * Get Mid URL
     *
     * @return  string
     */
    public function getMidUrl()
    {
        return self::MID_URL;
    }
    /**
     * Get Config Paths
     *
     * @param string $configPath
     * @param string $websiteId
     * @return bool
     */
    public function getConfig($configPath, $websiteId = null)
    {
        $data = $this->getConfigPathVariables();
        $configValue = $this->scopeConfig->getValue(
            $data[$configPath],
            ScopeInterface::SCOPE_WEBSITES
        );
        if ($websiteId) {
            $configValue = $this->scopeConfig->getValue(
                $data[$configPath],
                ScopeInterface::SCOPE_WEBSITES,
                $websiteId
            );
        }
        return $configValue;
    }

    /**
     * Check connected
     *
     * @return bool
     */
    public function isConnected()
    {
        $websites = $this->getWebsites();
      
        $websiteData = [];
        $data =$this->getConfigPathVariables();
        foreach ($websites as $key => $value) {
            $configValue = $this->scopeConfig->getValue(
                $data['spapi_oauth_code'],
                ScopeInterface::SCOPE_WEBSITES,
                $key
            );
            if (!empty($configValue)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Return array of active connections
     */
    public function getActiveConnections()
    {
        $websites = $this->getWebsites();
      
        $websiteData = [];
        $data =$this->getConfigPathVariables();
        foreach ($websites as $key => $value) {
            $configValue = $this->scopeConfig->getValue(
                $data['country'],
                ScopeInterface::SCOPE_WEBSITES,
                $key
            );
            if (!empty($configValue)) {
                $country = $this->countryInformationInterface->getCountryInfo($configValue);
                $websiteData[] = [$key, $configValue, $country->getFullNameLocale()];
            }
        }
        return $websiteData;
    }

    /**
     * Check if connection exist for a website
     *
     * @param int $websiteId
     * @param string $path
     * @return bool
     */
    public function isConnectedExist($websiteId, $path)
    {
        $data = $this->getConfigPathVariables();
        $configValue = $this->scopeConfig->getValue(
            $data[$path],
            ScopeInterface::SCOPE_WEBSITES,
            $websiteId
        );
        if (!empty($configValue)) {
            return true;
        }
        return false;
    }
    /**
     * Get Country Name
     *
     * @param int $websiteId
     * @param string $path
     * @return bool
     */
    public function getCountryName($websiteId, $path)
    {
        $data = $this->getConfigPathVariables();
        $configValue = $this->scopeConfig->getValue(
            $data[$path],
            ScopeInterface::SCOPE_WEBSITES,
            $websiteId
        );
        if (!empty($configValue)) {
            $country = $this->countryInformationInterface->getCountryInfo($configValue);
            return $country->getFullNameLocale();
        }
        return '';
    }
}
