<?php declare(strict_types=1);
/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */
namespace Zazmic\AmazonMCF\Plugin\Sales\Order;

class Grid
{
    /**
     * @var table
     */
    public static $table = 'sales_order_grid';
     /**
      * @var leftJoinTable
      */
    public static $leftJoinTable = 'sales_order';
    /**
     * After Search
     *
     * @param array $intercepter
     * @param array $collection
     * @return string
     */
    public function afterSearch($intercepter, $collection)
    {
        if ($collection->getMainTable() === $collection->getConnection()->getTableName(self::$table)) {
            $leftJoinTableName = $collection->getConnection()->getTableName(self::$leftJoinTable);
            $collection
                ->getSelect()
                ->joinLeft(
                    ['so'=>$leftJoinTableName],
                    "so.entity_id = main_table.entity_id",
                    [
                        'fulfilled_by_amazon' => 'so.fulfilled_by_amazon'
                    ]
                );
            $where = $collection->getSelect()->getPart(\Magento\Framework\DB\Select::WHERE);
            $collection->getSelect()->setPart(\Magento\Framework\DB\Select::WHERE, $where);
        }
        return $collection;
    }
}
