<?php declare(strict_types=1);
/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */
namespace Zazmic\AmazonMCF\Model;

use Magento\Framework\Model\AbstractModel;

class McfLog extends AbstractModel
{

    /**
     * Set resource model
     */
    public function _construct()
    {
        $this->_init(\Zazmic\AmazonMCF\Model\ResourceModel\McfLog::class);
    }
}
