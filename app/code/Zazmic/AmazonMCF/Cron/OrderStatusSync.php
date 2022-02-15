<?php declare(strict_types=1);
/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */
namespace Zazmic\AmazonMCF\Cron;

use Zazmic\AmazonMCF\Model\ConfigManager;

class OrderStatusSync
{
    /**
     * @var ConfigManager
     */
    private $configManager;

    /**
     * @param ConfigManager $configManager
     */
    public function __construct(
        ConfigManager $configManager
    ) {
        $this->configManager = $configManager;
    }

    /**
     * Orderstatus sync
     */
    public function execute()
    {
        $this->configManager->autoOrderStatusSync();
    }
}
