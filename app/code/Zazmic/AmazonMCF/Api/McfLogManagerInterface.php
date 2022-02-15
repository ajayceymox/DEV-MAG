<?php declare(strict_types=1);
/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */
namespace Zazmic\AmazonMCF\Api;

/**
 * Interface McfStoreManagerInterface
 * Method declarations for AMCF store add
 */
interface McfLogManagerInterface
{
    /**
     * Add Log data
     *
     * @param   array $data
     * @return  string
     */
    public function addLog($data);
}
