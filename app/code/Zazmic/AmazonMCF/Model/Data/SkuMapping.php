<?php declare(strict_types=1);
/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */
namespace Zazmic\AmazonMCF\Model\Data;

use Magento\Framework\Api\AbstractExtensibleObject;
use Zazmic\AmazonMCF\Api\Data\SkuMappingInterface;

class SkuMapping extends AbstractExtensibleObject implements SkuMappingInterface
{
    /**
     * Set Id
     *
     * @param int $id
     * @return Zazmic\AmazonMCF\Api\Data\SkuMappingInterface
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
     * @return Zazmic\AmazonMCF\Api\Data\SkuMappingInterface
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
     * @return Zazmic\AmazonMCF\Api\Data\SkuMappingInterface
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
     * Set Asin
     *
     * @param string $asin
     * @return Zazmic\AmazonMCF\Api\Data\SkuMappingInterface
     */
    public function setAsin($asin)
    {
        return $this->setData(self::ASIN, $asin);
    }

    /**
     * Get Asin
     *
     * @return string
     */
    public function getAsin()
    {
        return $this->_get(self::ASIN);
    }

    /**
     * Set Seller SKU
     *
     * @param string $sellerSku
     * @return Zazmic\AmazonMCF\Api\Data\SkuMappingInterface
     */
    public function setSellerSku($sellerSku)
    {
        return $this->setData(self::SELLER_SKU, $sellerSku);
    }

    /**
     * Get Seller SKU
     *
     * @return string
     */
    public function getSellerSku()
    {
        return $this->_get(self::SELLER_SKU);
    }
    /**
     * Set Sync Status
     *
     * @param string $syncStatus
     * @return Zazmic\AmazonMCF\Api\Data\SkuMappingInterface
     */
    public function setSyncStatus($syncStatus)
    {
        return $this->setData(self::SYNC_STATUS, $syncStatus);
    }

    /**
     * Get Sync Status
     *
     * @return string
     */
    public function getSyncStatus()
    {
        return $this->_get(self::SYNC_STATUS);
    }
    /**
     * Set Product Name
     *
     * @param string $productName
     * @return Zazmic\AmazonMCF\Api\Data\SkuMappingInterface
     */
    public function setAmazonProductName($productName)
    {
        return $this->setData(self::AMAZON_PRODUCT_NAME, $productName);
    }

    /**
     * Get Product Name
     *
     * @return string
     */
    public function getAmazonProductName()
    {
        return $this->_get(self::AMAZON_PRODUCT_NAME);
    }

    /**
     * Set Website
     *
     * @param string $website
     * @return Zazmic\AmazonMCF\Api\Data\SkuMappingInterface
     */
    public function setWebsite($website)
    {
        return $this->setData(self::WEBSITE, $website);
    }

    /**
     * Get Website
     *
     * @return string
     */
    public function getWebsite()
    {
        return $this->_get(self::WEBSITE);
    }

    /**
     * Set Status
     *
     * @param string $status
     * @return Zazmic\AmazonMCF\Api\Data\SkuMappingInterface
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Get Status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->_get(self::STATUS);
    }

    /**
     * Set CreatedAt
     *
     * @param string $createdAt
     * @return Zazmic\AmazonMCF\Api\Data\SkuMappingInterface
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * Get CreatedAt
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->_get(self::CREATED_AT);
    }

    /**
     * Set UpdatedAt
     *
     * @param string $updatedAt
     * @return Zazmic\AmazonMCF\Api\Data\SkuMappingInterface
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }

    /**
     * Get UpdatedAt
     *
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->_get(self::UPDATED_AT);
    }
}
