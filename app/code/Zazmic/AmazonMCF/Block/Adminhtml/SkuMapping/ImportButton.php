<?php declare(strict_types=1);
/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */
namespace Zazmic\AmazonMCF\Block\Adminhtml\SkuMapping;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class ImportButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * Button
     *
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Import SKUs'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save']],
                'form-role' => 'save',
            ],
            'sort_order' => 90,
        ];
    }
}
