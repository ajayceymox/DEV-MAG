<?php
declare(strict_types=1);

/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */

namespace Zazmic\AmazonMCF\Ui\Component\Listing\Column;
 
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class SyncStatus extends Column
{
    
    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$items) {
                if ($items['seller_sku']) {
                        $items['sync_status'] = $this->getStatus($items['sync_status']);
                } else {
                        $items['sync_status'] = "";
                }
            }
        }
        return $dataSource;
    }

    /**
     * GetStatus
     *
     * @param int $status
     * @return string
     */
    private function getStatus($status)
    {
        if ($status == 1) {
            return __('Enabled');
        } else {
            return __('Disabled');
        }
    }
}
