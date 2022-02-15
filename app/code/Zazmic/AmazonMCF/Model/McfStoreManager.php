<?php declare(strict_types=1);
/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */
namespace Zazmic\AmazonMCF\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Config\Model\ResourceModel\Config as ConfigResource;
use Zazmic\AmazonMCF\Helper\Data;
use Zazmic\AmazonMCF\Api\McfStoreManagerInterface;
use Zazmic\AmazonMCF\Model\ConfigManager;

/**
 * Class McfStoreManager
 * Manager class to handle add AMCF St
 */
class McfStoreManager implements McfStoreManagerInterface
{
    
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;
    /**
     * @var \Magento\Config\Model\ResourceModel\Config
     */
    private $configResource;
    /**
     * @var \Zazmic\AmazonMCF\Helper\Data
     */
    private $helper;
    /**
     * @var \Zazmic\AmazonMCF\Model\ConfigManager
     */
    private $configManager;
    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Config\Model\ResourceModel\Config $configResource
     * @param \Zazmic\AmazonMCF\Helper\Data $helper
     * @param \Zazmic\AmazonMCF\Model\ConfigManager $configManager
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ConfigResource $configResource,
        Data $helper,
        configManager $configManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->configResource = $configResource;
        $this->helper = $helper;
        $this->configManager = $configManager;
    }

    /**
     * Add AMCF Store programmatically
     *
     * @param   array $data
     * @return  string
     */
    public function addAmcfStore($data)
    {
        $configArr = $this->helper->getConfigPathVariables();
        try {
            $scopeId = $data['website'];
            foreach ($data as $key => $val) {
                if (in_array($key, array_keys($configArr)) && $key != 'website') {
                     $this->configManager->configUpdate(
                         $configArr[$key],
                         $val,
                         ScopeInterface::SCOPE_WEBSITES,
                         $scopeId
                     );
                }
            }
            return true;
        } catch (LocalizedException $e) {
            return $e->getMessage();
        }
    }

    /**
     * Get  Application ID
     *
     * @param   string $config_path
     * @return  string
     */
    public function getAppId($config_path)
    {
        $data = $this->helper->getConfigPathVariables();
        return $this->scopeConfig->getValue(
            $data[$config_path],
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Delete connected Config Values
     *
     * @param   array $data
     * @return  string
     */
    public function disConnectMcfAccount()
    {
        $configArr = $this->helper->getConfigPathVariables();
        $dataArr=["spapi_oauth_code" =>$this->helper->getConfig('spapi_oauth_code'),
                  "token" =>$this->helper->getConfig('token'),
                  "state" =>$this->helper->getConfig('state')];
        try {
            $flag = false;
            $this->configManager->getMcfRequest('disconnect', $dataArr);
            return $flag;
        } catch (LocalizedException $e) {
            return $e->getMessage();
        }
    }
    /**
     * Delete connected Config Values
     *
     * @param   array $data
     * @return  string
     */
    public function disConnectStore($data)
    {
        $configArr = $this->helper->getConfigPathVariables();
        $scopeId = $data['website'];
        try {
            $result = $this->disConnectMcfAccount();
            $flag = false;
            foreach ($data as $key => $val) {
                if (in_array($key, array_keys($configArr)) && $key != 'website') {
                    if ($this->configResource->deleteConfig(
                        $configArr[$key],
                        ScopeInterface::SCOPE_WEBSITES,
                        $scopeId
                    )) {
                        $flag = true;
                    }
                }
            }
            return $flag;
        } catch (LocalizedException $e) {
            return $e->getMessage();
        }
    }
}
