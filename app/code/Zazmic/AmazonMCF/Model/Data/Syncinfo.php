<?php declare(strict_types=1);
/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */
namespace Zazmic\AmazonMCF\Model\Data;

use Magento\Framework\Api\AbstractExtensibleObject;
use Zazmic\AmazonMCF\Api\Data\SyncinfoInterface;

class Syncinfo extends AbstractExtensibleObject implements SyncinfoInterface
{
    /**
     * Set Id
     *
     * @param int $id
     * @return Zazmic\AmazonMCF\Api\Data\SyncinfoInterface
     */
    public function setId($id)
    {
        return $this->setData(self::SYNC_ID, $id);
    }

    /**
     * Get Id
     *
     * @return int
     */
    public function getId()
    {
        return $this->_get(self::SYNC_ID);
    }

    /**
     * Set Sync Time
     *
     * @param int $syncTime
     * @return Zazmic\AmazonMCF\Api\Data\SyncinfoInterface
     */
    public function setSyncTime($syncTime)
    {
        return $this->setData(self::SYNC_TIME, $syncTime);
    }

    /**
     * Get Sync Time
     *
     * @return int
     */
    public function getSyncTime()
    {
        return $this->_get(self::SYNC_TIME);
    }

    /**
     * Set sync action
     *
     * @param string $syncAction
     * @return Zazmic\AmazonMCF\Api\Data\SyncinfoInterface
     */
    public function setSyncAction($syncAction)
    {
        return $this->setData(self::SYNC_ACTION, $syncAction);
    }

    /**
     * Get sync action
     *
     * @return string
     */
    public function getSyncAction()
    {
        return $this->_get(self::SYNC_ACTION);
    }

    /**
     * Set sync items count
     *
     * @param int $syncItemsCount
     * @return Zazmic\AmazonMCF\Api\Data\SyncinfoInterface
     */
    public function setSyncItemsCount($syncItemsCount)
    {
        return $this->setData(self::SYNC_ITEMS_COUNT, $syncItemsCount);
    }

    /**
     * Get sync items count
     *
     * @return int
     */
    public function getSyncItemsCount()
    {
        return $this->_get(self::SYNC_ITEMS_COUNT);
    }
}
