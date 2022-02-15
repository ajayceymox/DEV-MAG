<?php declare(strict_types=1);
/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */
namespace Zazmic\AmazonMCF\Api\Data;

interface SyncinfoInterface
{

    public const SYNC_ID = 'sync_id';
    public const SYNC_TIME = 'sync_time';
    public const SYNC_ACTION = 'sync_action';
    public const SYNC_ITEMS_COUNT = 'sync_items_count';

    /**
     * Set SyncId
     *
     * @param int $syncId
     * @return Zazmic\AmazonMCF\Api\Data\SyncinfoInterface
     */
    public function setId($syncId);
    /**
     * Get SyncId
     *
     * @return int
     */
    public function getId();
    /**
     * Set SyncTime
     *
     * @param string $syncTime
     * @return Zazmic\AmazonMCF\Api\Data\SyncinfoInterface
     */
    public function setSyncTime($syncTime);
    /**
     * Get SyncTime
     *
     * @return string
     */
    public function getSyncTime();
    /**
     * Set SyncAction
     *
     * @param int $syncAction
     * @return Zazmic\AmazonMCF\Api\Data\SyncinfoInterface
     */
    public function setSyncAction($syncAction);
    /**
     * Get SyncAction
     *
     * @return int
     */
    public function getSyncAction();
    /**
     * Set SyncItemsCount
     *
     * @param int $syncItemsCount
     * @return Zazmic\AmazonMCF\Api\Data\SyncinfoInterface
     */
    public function setSyncItemsCount($syncItemsCount);
    /**
     * Get SyncItemsCount
     *
     * @return int
     */
    public function getSyncItemsCount();
}
