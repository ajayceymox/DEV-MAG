<?php declare(strict_types=1);
/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */
namespace Zazmic\AmazonMCF\Block\Adminhtml;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Store\Api\WebsiteRepositoryInterface;
use Magento\Directory\Api\CountryInformationAcquirerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Zazmic\AmazonMCF\Helper\Data;
use Zazmic\AmazonMCF\Model\ConfigManager;
use Magento\Framework\App\Request\Http;

class Index extends Template
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;
    /**
     * @var StoreManagerInterface
     */
    private $storeManagerInterface;
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;
    /**
     * @var CountryInformationAcquirerInterface
     */
    private $countryInformationInterface;
    /**
     * @var Data
     */
    private $helper;
    /**
     * @var ConfigManager
     */
    private $configManager;

    /**
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param CountryInformationAcquirerInterface $countryInformationInterface
     * @param Data $helper
     * @param ConfigManager $configManager
     * @param Http $request
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        CountryInformationAcquirerInterface $countryInformationInterface,
        Data $helper,
        ConfigManager $configManager,
        Http $request
    ) {
        $this->urlBuilder = $context->getUrlBuilder();
        $this->scopeConfig = $scopeConfig;
        $this->countryInformationInterface = $countryInformationInterface;
        $this->helper = $helper;
        $this->configManager = $configManager;
        $this->request = $request;
        parent::__construct($context);
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->urlBuilder->getUrl($route, $params);
    }

    /**
     * Save Url
     */
    public function getSaveUrl()
    {
        return $this->getUrl('amazonmcf/setup/save');
    }

    /**
     * MCF allowed countries
     */
    public function getAmcfCountries()
    {
        return $this->helper->getAmcfCountries();
    }

    /**
     * Versions
     */
    public function getAmcfVersions()
    {
        return $this->helper->getAmcfVersions();
    }
    /**
     * MCF connect URL
     *
     * @param int $website
     */
    public function getConnectUrl($website)
    {
        $countryName = null;
        $countryCode = $this->helper->getConfig('country');
        $url=$this->helper->getMidUrl().'connect.php?';
        $url .= "application_id=amzn1.sp.solution.de7d46ce-825e-490c-8389-ac49f62be9bf";
        $url .= "&endpoint=".$countryCode;
        $url .= "&website=".$website;
        $url .= "&domain=".$this->getBaseUrl();
        return $url;
    }
    /**
     * Store Delete URL
     */
    public function getDeleteUrl()
    {
        $route = "amazonmcf/setup/delete";
        return $this->urlBuilder->getUrl($route);
    }
    /**
     * Generate url by route and parameters for re auth URL to set AMCF App
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getRetrunUrl()
    {
        $route = "amazonmcf/setup/reauthconfirm";
        return $this->urlBuilder->getBaseUrl().$route;
    }

    /**
     * Disconnect feature redirect
     */
    public function getDisconnectUrl()
    {
        $route = "amazonmcf/setup/disconnect";
        return $this->urlBuilder->getUrl($route);
    }

    /**
     * Is connected with MCF
     */
    public function isConnected()
    {
        $connected = $this->helper->isConnected();
        if ($connected) {
            return $connected;
        }
        return false;
    }
    /**
     * Is connected with MCF
     *
     * @param int $websiteId
     * @param string $path
     * @return boolean
     */
    public function isConnectedExist($websiteId, $path)
    {
        $connected = $this->helper->isConnectedExist($websiteId, $path);
        if ($connected) {
            return $connected;
        }
        return false;
    }
    /**
     * Check current Page
     *
     * @param   string $current
     * @return  string
     */
    public function checkCurrentPage($current)
    {
        if ($this->request->getRouteName() == 'amazonmcf' && $this->request->getControllerName() == $current) {
            return 'active';
        }
        return '';
    }

     /**
      * Return array of active websites
      */
    public function getWebsites()
    {
        return $this->helper->getWebsites();
    }

    /**
     * Return array of active connections
     */
    public function getActiveConnections()
    {
        return $this->helper->getActiveConnections();
    }
    /**
     * Return show add store button status
     */
    public function showAddStore()
    {
        if (count($this->getActiveConnections()) < count($this->getWebsites())) {
            return true;
        }
        return false;
    }

    /**
     * Get Website URL
     *
     * @param int $websiteId
     * @return string
     */
    public function getWebsiteUrl($websiteId)
    {
        $url = $this->helper->getConfig('base_url', $websiteId);
        if (!empty($url)) {
            return $url;
        }
        return '';
    }


}
