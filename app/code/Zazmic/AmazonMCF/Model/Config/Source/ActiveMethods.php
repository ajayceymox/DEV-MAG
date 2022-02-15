<?php declare(strict_types=1);
/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */
namespace Zazmic\AmazonMCF\Model\Config\Source;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Shipping\Model\Config;

class ActiveMethods implements \Magento\Framework\Option\ArrayInterface
{
    private const AMAZON_SHIPPING_CODE = 'amazonshipping';
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var Config
     */
    protected $shipconfig;
    /**
     * Initialize resource model
     * @param ScopeConfigInterface $scopeConfig
     * @param Config $shipconfig
     *
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Config $shipconfig
    ) {
        $this->shipconfig = $shipconfig;
        $this->scopeConfig = $scopeConfig;
    }
    /**
     * Active shipping Methods
     */
    public function getShippingMethods()
    {
        $activeCarriers = $this->shipconfig->getActiveCarriers();
        foreach ($activeCarriers as $carrierCode => $carrierModel) {
            $options = [];
            if ($carrierMethods = $carrierModel->getAllowedMethods()) {
                foreach ($carrierMethods as $methodCode => $method) {
                    if ($methodCode != self::AMAZON_SHIPPING_CODE) {
                        $code = $carrierCode . '_' . $methodCode;
                        $carrierTitle = $this->scopeConfig
                         ->getValue('carriers/'.$carrierCode.'/title');
                    }
                }
                
            }
            $methods[$code] =  $carrierTitle;
        }
        return $methods;
    }
    /**
     * Return array of options as key-value pairs
     */
    public function getOptionArray()
    {
        return $this->getShippingMethods();
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array
     */
    public function toOptionArray()
    {
        $opts = [];

        foreach (static::getOptionArray() as $key => $value) {
            $opts[] = [
                'label' => $value,
                'value' => $key,
            ];
        }

        return $opts;
    }

    /**
     * Retrieve option array with empty value
     *
     * @return string[]
     */
    public function getAllOptions()
    {
        return $this->toOptionArray();
    }

    /**
     * Retrieve option text by option value
     *
     * @param string $optionId
     * @return string
     */
    public function getOptionText($optionId)
    {
        $options = static::getOptionArray();

        return isset($options[$optionId]) ? $options[$optionId] : null;
    }
}
