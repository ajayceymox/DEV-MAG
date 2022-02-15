<?php declare(strict_types=1);
/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */
namespace Zazmic\AmazonMCF\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface McfLogSearchResultInterface extends SearchResultsInterface
{
    /**
     * Get Items
     *
     * @return RequestInterface[]
     */
    public function getItems();

    /**
     * Set Items
     *
     * @param RequestInterface[] $items
     * @return void
     */
    public function setItems(array $items);
}
