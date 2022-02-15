<?php declare(strict_types=1);
/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */
namespace Zazmic\AmazonMCF\Api\Data;

interface SkuMappingInterface
{
    public const ID = 'id';
    public const PRODUCT_ID = 'product_id';
    public const SKU = 'sku';
    public const ASIN = 'asin';
    public const AMAZON_PRODUCT_NAME = 'amazon_product_name';
    public const WEBSITE = 'website';
    public const STATUS = 'status';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';
    public const SELLER_SKU = 'seller_sku';
    public const SYNC_STATUS = 'sync_status';

    /**
     * Set Id
     *
     * @param int $id
     * @return Zazmic\AmazonMCF\Api\Data\SkuMappingInterface
     */
    public function setId($id);
    /**
     * Get Id
     *
     * @return int
     */
    public function getId();
    /**
     * Set ProductId
     *
     * @param int $productId
     * @return Zazmic\AmazonMCF\Api\Data\SkuMappingInterface
     */
    public function setProductId($productId);
    /**
     * Get ProductId
     *
     * @return int
     */
    public function getProductId();
    /**
     * Set Sku
     *
     * @param string $sku
     * @return Zazmic\AmazonMCF\Api\Data\SkuMappingInterface
     */
    public function setSku($sku);
    /**
     * Get Sku
     *
     * @return string
     */
    public function getSku();
    /**
     * Set Asin
     *
     * @param string $asin
     * @return Zazmic\AmazonMCF\Api\Data\SkuMappingInterface
     */
    public function setAsin($asin);
    /**
     * Get Asin
     *
     * @return string
     */
    public function getAsin();
    /**
     * Set Seller SKU
     *
     * @param string $sellerSku
     * @return Zazmic\AmazonMCF\Api\Data\SkuMappingInterface
     */
    public function setSellerSku($sellerSku);
    /**
     * Get Seller SKU
     *
     * @return string
     */
    public function getSellerSku();
    /**
     * Set Sync Status
     *
     * @param string $syncStatus
     * @return Zazmic\AmazonMCF\Api\Data\SkuMappingInterface
     */
    public function setSyncStatus($syncStatus);
    /**
     * Get Sync Status
     *
     * @return string
     */
    public function getSyncStatus();
    /**
     * Set Product Name
     *
     * @param string $productName
     * @return Zazmic\AmazonMCF\Api\Data\SkuMappingInterface
     */
    public function setAmazonProductName($productName);
    /**
     * Get Product Name
     *
     * @return string
     */
    public function getAmazonProductName();
    /**
     * Set Website
     *
     * @param string $website
     * @return Zazmic\AmazonMCF\Api\Data\SkuMappingInterface
     */
    public function setWebsite($website);
    /**
     * Get Website
     *
     * @return string
     */
    public function getWebsite();
    /**
     * Set Status
     *
     * @param string $status
     * @return Zazmic\AmazonMCF\Api\Data\SkuMappingInterface
     */
    public function setStatus($status);
    /**
     * Get Status
     *
     * @return string
     */
    public function getStatus();
    /**
     * Set CreatedAt
     *
     * @param string $createdAt
     * @return Zazmic\AmazonMCF\Api\Data\SkuMappingInterface
     */
    public function setCreatedAt($createdAt);
    /**
     * Get CreatedAt
     *
     * @return string
     */
    public function getCreatedAt();
    /**
     * Set UpdatedAt
     *
     * @param string $updatedAt
     * @return Zazmic\AmazonMCF\Api\Data\SkuMappingInterface
     */
    public function setUpdatedAt($updatedAt);
    /**
     * Get UpdatedAt
     *
     * @return string
     */
    public function getUpdatedAt();
}
