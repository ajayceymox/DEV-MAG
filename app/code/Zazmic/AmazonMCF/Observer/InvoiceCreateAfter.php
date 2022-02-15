<?php
declare(strict_types=1);

/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */

namespace Zazmic\AmazonMCF\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Directory\Model\ResourceModel\Region\Collection;
use Magento\Directory\Model\ResourceModel\Region\CollectionFactory;
use Zazmic\AmazonMCF\Model\ConfigManager;
use Zazmic\AmazonMCF\Model\FulfillmentOrderManager;
use Magento\Framework\Stdlib\DateTime\DateTimeFactory;
use Magento\Sales\Api\OrderItemRepositoryInterface;
use Magento\Framework\Serialize\Serializer\Json;

class InvoiceCreateAfter implements ObserverInterface
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;
    /**
     * @var ConfigManager
     */
    private $configManager;
    /**
     * @var DateTimeFactory
     */
    private $dateTimeFactory;
    /**
     * @var FulfillmentOrderManager
     */
    private $fulFillmentManager;
    /**
     * @var OrderItemRepositoryInterface
     */
    private $orderItem;
    /**
     * @var Json
     */
    private $json;

    /**
     * @param CollectionFactory $collectionFactory
     * @param ConfigManager $configManager
     * @param DateTimeFactory $dateTimeFactory
     * @param FulfillmentOrderManager $fulFillmentManager
     * @param OrderItemRepositoryInterface $orderItem
     * @param Json $json
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        ConfigManager $configManager,
        DateTimeFactory $dateTimeFactory,
        FulfillmentOrderManager $fulFillmentManager,
        OrderItemRepositoryInterface $orderItem,
        Json $json
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->configManager = $configManager;
        $this->dateTimeFactory = $dateTimeFactory;
        $this->fulFillmentManager = $fulFillmentManager;
        $this->orderItemRepo = $orderItem;
        $this->json = $json;
    }
    /**
     * Execute
     *
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getInvoice()->getOrder();
        $orderId = $order->getEntityId();
        $orderIncrementId = $order->getIncrementId();
        $orderItemsData = $this->fulFillmentManager->getOrderItemsSellerSku($orderId);
        if ($order->getFulfilledByAmazon() != 0) {
            $shippingAddress = $order->getShippingAddress();
            $regionData = $this->collectionFactory->create()
                ->addRegionNameFilter($shippingAddress->getRegion())
                ->getFirstItem()
                ->toArray();
            $regionCode = '';
            if (isset($regionData['code'])) {
                $regionCode = $regionData['code'];
            }
            if (isset($shippingAddress['lastname'])) {
                $lastname = $shippingAddress->getLastname();
            }
            $street = $shippingAddress->getStreet();
            $street1 = isset($street[1]) ? $street[1] : '';
            $street2 = isset($street[2]) ? $street[2] : '';
            $data = [];
            $shippingMcfMethod='';
            $shippingMethod = $order->getShippingMethod();
            $method = explode("_", $shippingMethod);
            if ($method[0] != 'amazonshipping') {
                $shippingMcfMethod = $this->configManager->getConfigFromScope(
                    'shipping_config_default_method',
                    $order->getStore()->getWebsiteId()
                );
                if ($shippingMcfMethod!='') {
                    $shippingMcfMethod=$this->json->unserialize($shippingMcfMethod);
                    $shippingMethod = ucfirst($shippingMcfMethod[$shippingMethod]);
                } else {
                    $shippingMethod = 'Standard';
                }
            } else {
                $shippingMethod = ucfirst($method[1]);
            }

            $orderDate = $order->getCreatedAt();
            $orderDate = strtotime($orderDate);
            $orderDate = date('Y-m-d\TH:i:s.Z\Z', $orderDate);
            $data['sellerFulfillmentOrderId'] = $orderIncrementId;
            $data['displayableOrderComment'] = 'Magento Order Id: '.$orderIncrementId;
            $data['displayableOrderDate'] = $orderDate;
            $data['displayableOrderId'] = $orderIncrementId;
            $data['shippingSpeedCategory'] = $shippingMethod;
            $data['fulfillmentAction'] = 'Ship';
            $data['shipFromCountryCode'] = 'US';
            $address=[
                "name"     => $shippingAddress->getFirstname().' '.$shippingAddress->getLastname(),
                "addressLine1"  => $street[0],
                "addressLine2"  => $street1,
                "addressLine3"  => $street2,
                "city"          => $shippingAddress->getCity(),
                "stateOrRegion" => $regionCode,
                "postalCode"    => $shippingAddress->getPostcode(),
                "countryCode"   => $shippingAddress->getCountryId(),
                "districtOrCounty" => $regionCode,
                "phone"         => $shippingAddress->getTelephone()
                ];
            $data['destinationAddress'] = $address;
            $data['items'] = $orderItemsData['items'];
            $data['notificationEmails'] = $order->getCustomerEmail();
            $data['fulfillmentPolicy'] = 'FillOrKill';
            $orderData = ['data' => $data];
            $response = $this->fulFillmentManager->createOrder($orderData);
            if (empty($response)) {
                $status = $this->fulFillmentManager->getAmcfOrderStatus($orderIncrementId);
                $shipment = $this->fulFillmentManager->createShipment($orderId);
                if ($status != 404) {
                    $fulfillmentOrderStatus = '';
                    $fulfillmentShipmentStatus = '';
                    if (isset($status['fulfillmentOrderStatus'])) {
                        $fulfillmentOrderStatus = $status['fulfillmentOrderStatus'];
                        $order->setAmazonOrderStatus($fulfillmentOrderStatus);
                    }
                    if (isset($status['fulfillmentShipmentStatus'])) {
                        $fulfillmentShipmentStatus = $status['fulfillmentShipmentStatus'];
                        $order->setAmazonShipmentStatus($fulfillmentShipmentStatus);
                    }
                    $sellerFulfillmentOrderId = '';
                    if (isset($status['sellerFulfillmentOrderId'])) {
                        $sellerFulfillmentOrderId = $status['sellerFulfillmentOrderId'];
                    }
                    if (isset($status['itemData'])) {
                        foreach ($order->getAllItems() as $item) {
                            foreach ($status['itemData'] as $itemData) {
                                if (isset($itemData['sellerFulfillmentOrderItemId'])) {
                                    $itemId = $itemData['sellerFulfillmentOrderItemId'];
                                    $itemSingle = explode("-", $itemId);
                                    $itemSingleId = $itemSingle[1];
                                    if ($itemSingleId == $item->getItemId()) {
                                        $item->setSellerFulfillmentOrderId($sellerFulfillmentOrderId);
                                        $item->setAmazonOrderStatus($fulfillmentOrderStatus);
                                        $item->setAmazonShipmentStatus($fulfillmentShipmentStatus);
                                        $item->setSellerFulfillmentOrderItemId($itemId);
                                        $item->setQtyShipped($shipment[$item->getItemId()]);
                                        if (isset($status['shipmentData'])) {
                                            foreach ($status['shipmentData'] as $shipmentData) {
                                                if ($shipmentData['sellerFulfillmentOrderItemId'] == $itemId) {
                                                    $item->setAmazonPackageNumber($shipmentData['packageNumber']);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
