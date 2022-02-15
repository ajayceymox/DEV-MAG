<?php declare(strict_types=1);
/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */
namespace Zazmic\AmazonMCF\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Zazmic\AmazonMCF\Api\Data\InventorySyncinfoInterface;
use Zazmic\AmazonMCF\Api\Data\InventorySyncinfoSearchResultInterface;

interface InventorySyncinfoRepositoryInterface
{
    /**
     * Get List
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return InventorySyncinfoSearchResultInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
    /**
     * Get By Id
     *
     * @param int $id
     * @return InventorySyncinfoInterface
     * @throws NoSuchEntityException
     */
    public function getById($id);

    /**
     * Save
     *
     * @param InventorySyncinfoInterface $inventorySyncinfo
     * @return InventorySyncinfoInterface
     */
    public function save(InventorySyncinfoInterface $inventorySyncinfo);

    /**
     * Delete
     *
     * @param InventorySyncinfoInterface $inventorySyncinfo
     * @return void
     */
    public function delete(InventorySyncinfoInterface $inventorySyncinfo);
    
    /**
     * Delete by id
     *
     * @param int $id
     * @return boolean
     */
    public function deleteById($id);
}
