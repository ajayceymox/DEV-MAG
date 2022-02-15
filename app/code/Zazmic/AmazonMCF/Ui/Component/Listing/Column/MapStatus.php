<?php
declare(strict_types=1);

/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */

namespace Zazmic\AmazonMCF\Ui\Component\Listing\Column;
 
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class MapStatus extends Column
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
                if ($items['mcf_enabled'] == 1) {
                    $items['mcf_enabled'] = $this->getStatus($items['mcf_enabled']);
                } else {
                    $items['mcf_enabled'] = $this->getStatus(0);
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
            return __('Mapped');
        } else {
            return __('Not Mapped');
        }
    }
}
