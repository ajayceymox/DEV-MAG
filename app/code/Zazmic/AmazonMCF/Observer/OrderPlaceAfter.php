<?php declare(strict_types=1);
/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */
namespace Zazmic\AmazonMCF\Observer;
 
use Magento\Framework\Event\ObserverInterface;
use Zazmic\AmazonMCF\Model\FulfillmentOrderManager;
 
class OrderPlaceAfter implements ObserverInterface
{
    /**
     * @var FulfillmentOrderManager
     */
    protected $fulfillmentOrderManager;
    /**
     * @param FulfillmentOrderManager $fulfillmentOrderManager
     */
    public function __construct(
        FulfillmentOrderManager $fulfillmentOrderManager
    ) {
         $this->fulfillmentOrderManager = $fulfillmentOrderManager;
    }
    /**
     * Execute
     *
     * @param Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $orderData = $this->fulfillmentOrderManager->getOrderItemsStatus($order);
        if (!empty($orderData) && isset($orderData['amazon-mcf-status'])) {
            $order->setFulfilledByAmazon($orderData['amazon-mcf-status']);
            if ($orderData['amazon-mcf-status'] == 1 || $orderData['amazon-mcf-status'] == 2) {
                foreach ($order->getAllItems() as $item) {
                    if (isset($orderData['mcf_products']) &&
                    in_array($item->getProductId(), $orderData['mcf_products'])) {
                        $item->setFulfilledByAmazon(1);
                    } else {
                        $item->setFulfilledByAmazon(0);
                    }
                }
            }
        }
    }
}
