<?php declare(strict_types=1);
/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */
namespace Zazmic\AmazonMCF\Api;

/**
 * Interface McfStoreManagerInterface
 * Method declarations for AMCF store add
 */
interface McfStoreManagerInterface
{
    /**
     * Add AMCF Store programmatically
     *
     * @param   array $data
     * @return  string
     */
    public function addAmcfStore($data);

    /**
     * Delete connected Config Values
     *
     * @param   array $data
     * @return  string
     */
    public function disConnectStore($data);
}
