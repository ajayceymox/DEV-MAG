<?php
declare(strict_types=1);

/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */

namespace Zazmic\AmazonMCF\Block\Adminhtml\Inventory;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class SyncButton extends Template
{
    /**
     * Block template.
     *
     * @var string
     */
    protected $_template = 'Inventory/syncbuton.phtml';
    /**
     * @var Context
     */
    private $context;
    /**
     * @var TimezoneInterface
     */
    private $timezone;
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param Context $context
     * @param TimezoneInterface $timezone
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        TimezoneInterface $timezone,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->timezone             = $timezone;
        $this->scopeConfig          = $scopeConfig;
        $this->storeManager         = $storeManager;
        parent::__construct($context);
    }

    /**
     * Get Last Synced Date
     *
     * @param int $website
     * @return string
     */
    public function getLastSynced($website)
    {
        try {
            $configValue = $this->scopeConfig->getValue(
                'zazmic/inventory_config/last_synced',
                \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITES,
                $website
            );
            if (isset($configValue) && $configValue != '') {
                $dateTimeZone = $this->timezone->date(new \DateTime($configValue))->format('Y-m-d H:i:s');
                return $dateTimeZone;
            }
            return false;
        } catch (\Exception $e) {
            return false;

        }
    }
}
