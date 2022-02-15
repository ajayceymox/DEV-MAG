<?php declare(strict_types=1);
/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */
namespace Zazmic\AmazonMCF\Model\Data;

use Magento\Framework\Api\AbstractExtensibleObject;
use Zazmic\AmazonMCF\Api\Data\McfLogInterface;

class McfLog extends AbstractExtensibleObject implements McfLogInterface
{
    /**
     * Set Id
     *
     * @param int $id
     * @return Zazmic\AmazonMCF\Api\Data\McfLogInterface
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
     * Set Area
     *
     * @param string $area
     * @return Zazmic\AmazonMCF\Api\Data\McfLogInterface
     */
    public function setArea($area)
    {
        return $this->setData(self::AREA, $area);
    }

    /**
     * Get Area
     *
     * @return string
     */
    public function getArea()
    {
        return $this->_get(self::AREA);
    }

    /**
     * Set Type
     *
     * @param string $type
     * @return Zazmic\AmazonMCF\Api\Data\McfLogInterface
     */
    public function setType($type)
    {
        return $this->setData(self::TYPE, $type);
    }

    /**
     * Get Type
     *
     * @return string
     */
    public function getType()
    {
        return $this->_get(self::TYPE);
    }

    /**
     * Set Details
     *
     * @param string $details
     * @return Zazmic\AmazonMCF\Api\Data\McfLogInterface
     */
    public function setDetails($details)
    {
        return $this->setData(self::DETAILS, $details);
    }

    /**
     * Get Details
     *
     * @return string
     */
    public function getDetails()
    {
        return $this->_get(self::DETAILS);
    }

    /**
     * Set User
     *
     * @param string $user
     * @return Zazmic\AmazonMCF\Api\Data\McfLogInterface
     */
    public function setUser($user)
    {
        return $this->setData(self::USER, $user);
    }

    /**
     * Get User
     *
     * @return string
     */
    public function getUser()
    {
        return $this->_get(self::USER);
    }

    /**
     * Set Time
     *
     * @param string $time
     * @return Zazmic\AmazonMCF\Api\Data\McfLogInterface
     */
    public function setTime($time)
    {
        return $this->setData(self::TIME, $time);
    }

    /**
     * Get Time
     *
     * @return string
     */
    public function getTime()
    {
        return $this->_get(self::TIME);
    }
}
