<?php declare(strict_types=1);
/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */

namespace Zazmic\AmazonMCF\Api\Data;

interface McfLogInterface
{

    public const ID = 'id';
    public const AREA = 'area';
    public const TYPE = 'type';
    public const DETAILS = 'details';
    public const USER = 'user';
    public const TIME = 'time';

    /**
     * Set Id
     *
     * @param int $id
     * @return Zazmic\AmazonMCF\Api\Data\McfLogInterface
     */
    public function setId($id);
    /**
     * Get Id
     *
     * @return int
     */
    public function getId();
    /**
     * Set Area
     *
     * @param string $area
     * @return Zazmic\AmazonMCF\Api\Data\McfLogInterface
     */
    public function setArea($area);
    /**
     * Get Area
     *
     * @return string
     */
    public function getArea();
    /**
     * Set Type
     *
     * @param string $type
     * @return Zazmic\AmazonMCF\Api\Data\McfLogInterface
     */
    public function setType($type);
    /**
     * Get Type
     *
     * @return string
     */
    public function getType();
    /**
     * Set Details
     *
     * @param string $details
     * @return Zazmic\AmazonMCF\Api\Data\McfLogInterface
     */
    public function setDetails($details);
    /**
     * Get Details
     *
     * @return string
     */
    public function getDetails();
    /**
     * Set User
     *
     * @param string $user
     * @return Zazmic\AmazonMCF\Api\Data\McfLogInterface
     */
    public function setUser($user);
    /**
     * Get User
     *
     * @return string
     */
    public function getUser();
    /**
     * Set Time
     *
     * @param string $time
     * @return Zazmic\AmazonMCF\Api\Data\McfLogInterface
     */
    public function setTime($time);
    /**
     * Get Time
     *
     * @return string
     */
    public function getTime();
}
