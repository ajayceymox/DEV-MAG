<?php declare (strict_types = 1);
/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */
namespace Zazmic\AmazonMCF\Block\Adminhtml\SkuMapping;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class AutoMapButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * AutoMap Button
     */

    public function getButtonData()
    {
        $message = __('If you proceed, this will attempt to map Magento SKUs to ');
        $message .= __('Amazon SKUs based on exact SKU value matches.');
        $message .= __('Any SKUs that are an exact match will be mapped.');
        $message .= '<br/><br/>';
        $message .= __('Do you want to proceed?');
        return [
            'label' => __('AutoMap'),
            'class' => 'primary',
            'on_click' => "confirmSetLocation('{$message}', '{$this->getUrl('*/*/automap')}')",
        ];
    }
}
