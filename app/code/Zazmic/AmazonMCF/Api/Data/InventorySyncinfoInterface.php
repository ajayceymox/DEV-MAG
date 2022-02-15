<?php declare(strict_types=1);
/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */
namespace Zazmic\AmazonMCF\Api\Data;

interface InventorySyncinfoInterface
{

    public const ID = 'id';
    public const SKU = 'sku';
    public const PRODUCT_ID = 'product_id';
    public const PREV_QTY = 'prev_qty';
    public const UPDATED_QTY = 'updated_qty';
    public const LAST_SYNC_TIME = 'last_sync_time';

    /**
     * Set Id
     *
     * @param int $id
     * @return Zazmic\AmazonMCF\Api\Data\InventorySyncinfoInterface
     */
    public function setId($id);
    /**
     * Get Id
     *
     * @return int
     */
    public function getId();
    /**
     * Set Sku
     *
     * @param string $sku
     * @return Zazmic\AmazonMCF\Api\Data\InventorySyncinfoInterface
     */
    public function setSku($sku);
    /**
     * Get Sku
     *
     * @return string
     */
    public function getSku();
    /**
     * Set ProductId
     *
     * @param int $productId
     * @return Zazmic\AmazonMCF\Api\Data\InventorySyncinfoInterface
     */
    public function setProductId($productId);
    /**
     * Get ProductId
     *
     * @return int
     */
    public function getProductId();
    /**
     * Set PrevQty
     *
     * @param int $prevQty
     * @return Zazmic\AmazonMCF\Api\Data\InventorySyncinfoInterface
     */
    public function setPrevQty($prevQty);
    /**
     * Get PrevQty
     *
     * @return int
     */
    public function getPrevQty();
    /**
     * Set UpdatedQty
     *
     * @param int $updatedQty
     * @return Zazmic\AmazonMCF\Api\Data\InventorySyncinfoInterface
     */
    public function setUpdatedQty($updatedQty);
    /**
     * Get UpdatedQty
     *
     * @return int
     */
    public function getUpdatedQty();
    /**
     * Set LastSyncTime
     *
     * @param string $lastSyncTime
     * @return Zazmic\AmazonMCF\Api\Data\InventorySyncinfoInterface
     */
    public function setLastSyncTime($lastSyncTime);
    /**
     * Get LastSyncTime
     *
     * @return string
     */
    public function getLastSyncTime();
}
