<?php declare(strict_types=1);
/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */
namespace Zazmic\AmazonMCF\Api;

/**
 * Interface ConfigInterface
 * Method declarations for AMCF Config Details
 */
interface ConfigInterface
{
    /**
     * Check if contacts module is enabled
     *
     * @return bool
     */
    public function isEnabled();

    /**
     * Check if connected
     *
     * @return bool
     */
    public function isConnected();
}
