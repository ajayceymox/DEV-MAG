<?php declare(strict_types=1);
/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */
namespace Zazmic\AmazonMCF\Model\ResourceModel\McfLog;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Zazmic\AmazonMCF\Model\McfLog as RequestModel;
use Zazmic\AmazonMCF\Model\ResourceModel\McfLog as RequestResourceModel;

class Collection extends AbstractCollection
{
    /**
     * @var _idFieldName
     */
    protected $_idFieldName = 'id';
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
