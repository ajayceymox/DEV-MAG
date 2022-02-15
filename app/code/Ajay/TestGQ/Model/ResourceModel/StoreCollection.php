<?php
declare(strict_types=1);

namespace Ajay\TestGQ\Model\ResourceModel;

use Ajay\TestGQ\Model\ResourceModel\Store as StoreResourceModel;
use Ajay\TestGQ\Model\Store as StoreModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class StoreCollection extends AbstractCollection
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(StoreModel::class, StoreResourceModel::class);
    }
}