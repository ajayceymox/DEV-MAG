<?php declare (strict_types = 1);
/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */
namespace Zazmic\AmazonMCF\Model\Config;

use Magento\Framework\App\Config\Value;
use Magento\Framework\Exception\LocalizedException;

class Cron extends Value
{
    /**
     * BeforeSave
     *
     * @return $this
     * @throws LocalizedException|\Zend_Validate_Exception
     */
    public function beforeSave()
    {
        $value     = $this->getValue();
        $validator = \Zend_Validate::is(
            $value,
            'Regex',
            ['pattern' => '/^[0-9,\-\?\/\*\ ]+$/']
        );

        if (!$validator) {
            $message = __(
                'Please correct cron expression: "%1".',
                $value
            );
            throw new LocalizedException($message);
        }

        return $this;
    }
}
