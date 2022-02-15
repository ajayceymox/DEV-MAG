<?php declare(strict_types=1);
/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */
namespace Zazmic\AmazonMCF\Model\ResourceModel\Syncinfo;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

use Zazmic\AmazonMCF\Model\Syncinfo as RequestModel;
use Zazmic\AmazonMCF\Model\ResourceModel\Syncinfo as RequestResourceModel;

class Collection extends AbstractCollection
{

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            RequestModel::class,
            RequestResourceModel::class
        );
    }
}
