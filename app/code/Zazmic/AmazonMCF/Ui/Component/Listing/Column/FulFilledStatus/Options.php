<?php
declare(strict_types=1);

/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */

namespace Zazmic\AmazonMCF\Ui\Component\Listing\Column\FulFilledStatus;

use Magento\Framework\Data\OptionSourceInterface;

class Options implements OptionSourceInterface
{
    /**
     * @var array
     */
    protected $options;

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options !== null) {
            return $this->options;
        }

        $this->options = [
            [
                'label' => __('MCF'),
                'value' => 2
            ],
            [
                'label' => __('MCF'),
                'value' => 1
            ],
            [
                'label' => __('NON MCF'),
                'value' => 0
            ]
        ];

        return $this->options;
    }
}
