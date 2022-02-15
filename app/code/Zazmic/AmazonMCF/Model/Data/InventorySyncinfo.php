<?php declare(strict_types=1);
/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */
namespace Zazmic\AmazonMCF\Model\Data;

use Magento\Framework\Api\AbstractExtensibleObject;
use Zazmic\AmazonMCF\Api\Data\InventorySyncinfoInterface;

class InventorySyncinfo extends AbstractExtensibleObject implements InventorySyncinfoInterface
{
    /**
     * Set Id
     *
     * @param int $id
     * @return Zazmic\AmazonMCF\Api\Data\InventorySyncinfoInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * Get Id
     *
     * @return int
     */
    public function getId()
    {
        return $this->_get(self::ID);
    }

    /**
     * Set ProductId
     *
     * @param int $productId
     * @return Zazmic\AmazonMCF\Api\Data\InventorySyncinfoInterface
     */
    public function setProductId($productId)
    {
        return $this->setData(self::PRODUCT_ID, $productId);
    }

    /**
     * Get ProductId
     *
     * @return int
     */
    public function getProductId()
    {
        return $this->_get(self::PRODUCT_ID);
    }

    /**
     * Set Sku
     *
     * @param string $sku
     * @return Zazmic\AmazonMCF\Api\Data\InventorySyncinfoInterface
     */
    public function setSku($sku)
    {
        return $this->setData(self::SKU, $sku);
    }

    /**
     * Get Sku
     *
     * @return string
     */
    public function getSku()
    {
        return $this->_get(self::SKU);
    }

    /**
     * Set Prev Qty
     *
     * @param string $prevQty
     * @return Zazmic\AmazonMCF\Api\Data\InventorySyncinfoInterface
     */
    public function setPrevQty($prevQty)
    {
        return $this->setData(self::PREV_QTY, $prevQty);
    }

    /**
     * Get Prev Qty
     *
     * @return string
     */
    public function getPrevQty()
    {
        return $this->_get(self::PREV_QTY);
    }

    /**
     * Set Updated Qty
     *
     * @param string $updatedQty
     * @return Zazmic\AmazonMCF\Api\Data\InventorySyncinfoInterface
     */
    public function setUpdatedQty($updatedQty)
    {
        return $this->setData(self::UPDATED_QTY, $updatedQty);
    }

    /**
     * Get Updated Qty
     *
     * @return string
     */
    public function getUpdatedQty()
    {
        return $this->_get(self::UPDATED_QTY);
    }

    /**
     * Set Last Sync Time
     *
     * @param string $lastSyncTime
     * @return Zazmic\AmazonMCF\Api\Data\InventorySyncinfoInterface
     */
    public function setLastSyncTime($lastSyncTime)
    {
        return $this->setData(self::LAST_SYNC_TIME, $lastSyncTime);
    }

    /**
     * Get Last Sync Time
     *
     * @return string
     */
    public function getLastSyncTime()
    {
        return $this->_get(self::LAST_SYNC_TIME);
    }
}
