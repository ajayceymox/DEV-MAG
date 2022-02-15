<?php declare(strict_types=1);
/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */
namespace Zazmic\AmazonMCF\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class InventoryConfig extends AbstractDb
{

    /**
     * Initialize resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init("mcf_inventory_config", "id");
    }
}
