<?php declare(strict_types=1);
/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */
namespace Zazmic\AmazonMCF\Api;

/**
 * Interface ConfigInterface
 * Method declarations for AMCF Config Details
 */
interface FullfillmentInterface
{
    /**
     * Check if contacts module is enabled
     *
     * @return bool
     */
    public function isMcfEnabled();
}
