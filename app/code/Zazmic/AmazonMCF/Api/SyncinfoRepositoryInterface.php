<?php declare(strict_types=1);
/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */
namespace Zazmic\AmazonMCF\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Zazmic\AmazonMCF\Api\Data\SyncinfoInterface;
use Zazmic\AmazonMCF\Api\Data\SyncinfoSearchResultInterface;

interface SyncinfoRepositoryInterface
{
    /**
     * Get List
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return SyncinfoSearchResultInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
    /**
     * Get By Id
     *
     * @param int $id
     * @return SyncinfoInterface
     * @throws NoSuchEntityException
     */
    public function getById($id);

    /**
     * Save
     *
     * @param SyncinfoInterface $syncInfo
     * @return SyncinfoInterface
     */
    public function save(SyncinfoInterface $syncInfo);

    /**
     * Delete
     *
     * @param SyncinfoInterface $syncInfo
     * @return void
     */
    public function delete(SyncinfoInterface $syncInfo);
    
    /**
     * Delete by id
     *
     * @param int $id
     * @return boolean
     */
    public function deleteById($id);
}
