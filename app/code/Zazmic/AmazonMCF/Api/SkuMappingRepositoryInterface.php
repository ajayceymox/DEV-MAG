<?php declare(strict_types=1);
/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */
namespace Zazmic\AmazonMCF\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Zazmic\AmazonMCF\Api\Data\SkuMappingInterface;
use Zazmic\AmazonMCF\Api\Data\SkuMappingSearchResultInterface;

/**
 * Interface SkuMappingRepositoryInterface
 * Declaration for skuMapping CRUD operations
 */
interface SkuMappingRepositoryInterface
{
    /**
     * Get List
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return SkuMappingSearchResultInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
    /**
     * Get By Id
     *
     * @param int $id
     * @return SkuMappingInterface
     * @throws NoSuchEntityException
     */
    public function getById($id);
    
    /**
     * Get By ProductId
     *
     * @param int $productId
     * @return SkuMappingInterface
     * @throws NoSuchEntityException
     */
    public function getByProductId($productId);

    /**
     * Save
     *
     * @param SkuMappingInterface $skuMapping
     * @return SkuMappingInterface
     */
    public function save(SkuMappingInterface $skuMapping);

    /**
     * Delete
     *
     * @param SkuMappingInterface $skuMapping
     * @return void
     */
    public function delete(SkuMappingInterface $skuMapping);
    
    /**
     * Delete by id
     *
     * @param int $id
     * @return boolean
     */
    public function deleteById($id);
}
