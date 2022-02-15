<?php declare(strict_types=1);
/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */
namespace Zazmic\AmazonMCF\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class SyncStatus implements OptionSourceInterface
{
    /**
     * @var array Possible status values
     */
    protected static $allowedStatus = [
        '15'   => 'Every 15 mins',
        '30'   => 'Every 30 mins',
        '60'    => 'Every 1 hr',
        '120'    => 'Every 2 hr',
    ];
    /**
     * GetOptionArray
     *
     * @return string[]
     */
    public function getOptionArray()
    {
        return static::$allowedStatus;
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
     * Retrieve option array
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
