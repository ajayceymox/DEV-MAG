<?php declare(strict_types=1);
/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */
namespace Zazmic\AmazonMCF\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Zazmic\AmazonMCF\Api\Data\McfLogInterface;
use Zazmic\AmazonMCF\Api\Data\McfLogSearchResultInterface;

/**
 * Interface McfLogRepositoryInterface
 * Declaration for mcfLog CRUD operations
 */
interface McfLogRepositoryInterface
{
    /**
     * Get List
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return McfLogSearchResultInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
    /**
     * Get By Id
     *
     * @param int $id
     * @return McfLogInterface
     * @throws NoSuchEntityException
     */
    public function getById($id);

    /**
     * Save
     *
     * @param McfLogInterface $mcfLog
     * @return McfLogInterface
     */
    public function save(McfLogInterface $mcfLog);

    /**
     * Delete
     *
     * @param McfLogInterface $mcfLog
     * @return void
     */
    public function delete(McfLogInterface $mcfLog);
    
    /**
     * Delete by id
     *
     * @param int $id
     * @return boolean
     */
    public function deleteById($id);
}
