<?php declare(strict_types=1);
/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */
namespace Zazmic\AmazonMCF\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Zazmic\AmazonMCF\Api\SkuMappingRepositoryInterface;
use Zazmic\AmazonMCF\Api\Data\SkuMappingInterface;
use Zazmic\AmazonMCF\Api\Data\SkuMappingInterfaceFactory;
use Zazmic\AmazonMCF\Helper\Data;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\Json\Encoder;
use Magento\Framework\Json\Decoder;
use Magento\Sales\Model\Convert\Order;
use Magento\Shipping\Model\ShipmentNotifier;
use Magento\Sales\Model\ResourceModel\Order as OrderResourceModel;
use Magento\Sales\Api\OrderItemRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;

class FulfillmentOrderManager
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;
    /**
     * @var Data
     */
    private $helper;
    /**
     * @var Curl
     */
    private $curlClient;
    /**
     * @var SkuMappingRepositoryInterface
     */
    private $skuMappingRepository;
    /**
     * @var SkuMappingInterfaceFactory
     */
    private $skuMappingInterfaceFactory;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var TimezoneInterface
     */
    private $date;
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;
    /**
     * @var Encoder
     */
    private $jsonEncoder;
    /**
     * @var Decoder
     */
    private $jsonDecoder;
    /**
     * @var Order
     */
    private $convertOrder;
    /**
     * @var ShipmentNotifier
     */
    private $shipmentNotifier;
    /**
     * @var OrderResourceModel
     */
    private $orderResourceModel;
    /**
     * @var OrderItemRepositoryInterface
     */
    private $orderItem;
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param Data $helper
     * @param Curl $curlClient
     * @param SkuMappingRepositoryInterface $skuMappingRepository
     * @param SkuMappingInterfaceFactory $skuMappingInterfaceFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param StoreManagerInterface $storeManager
     * @param TimezoneInterface $date
     * @param OrderRepositoryInterface $orderRepository
     * @param Encoder $jsonEncoder
     * @param Decoder $jsonDecoder
     * @param Order $convertOrder
     * @param ShipmentNotifier $shipmentNotifier
     * @param OrderResourceModel $orderResourceModel
     * @param OrderItemRepositoryInterface $orderItem
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Data $helper,
        Curl $curlClient,
        SkuMappingRepositoryInterface $skuMappingRepository,
        SkuMappingInterfaceFactory $skuMappingInterfaceFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        StoreManagerInterface $storeManager,
        TimezoneInterface $date,
        OrderRepositoryInterface $orderRepository,
        Encoder $jsonEncoder,
        Decoder $jsonDecoder,
        Order $convertOrder,
        ShipmentNotifier $shipmentNotifier,
        OrderResourceModel $orderResourceModel,
        OrderItemRepositoryInterface $orderItem,
        ProductRepositoryInterface $productRepository
    ) {
        $this->helper = $helper;
        $this->scopeConfig = $scopeConfig;
        $this->curl = $curlClient;
        $this->skuMappingRepository = $skuMappingRepository;
        $this->skuMappingInterfaceFactory = $skuMappingInterfaceFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->storeManager = $storeManager;
        $this->date = $date;
        $this->orderRepository = $orderRepository;
        $this->jsonEncoder = $jsonEncoder;
        $this->jsonDecoder = $jsonDecoder;
        $this->convertOrder = $convertOrder;
        $this->shipmentNotifier = $shipmentNotifier;
        $this->orderResourceModel = $orderResourceModel;
        $this->orderItemRepo = $orderItem;
        $this->productRepository = $productRepository;
    }

    /**
     * Get order items by order id
     *
     * @param int $orderId
     * @return array
     */
    public function getOrderItemsSellerSku($orderId)
    {
        $orderItems = [];
        $mcfStatus = [];
        $order = $this->orderRepository->get($orderId);
        $i = 0;
        foreach ($order->getAllItems() as $item) {
            $productId = $item->getProductId();
            $productType = $this->productRepository->getById($productId)->getTypeId();
            if ($productType == 'simple') {
                $mappingData = $this->skuMappingRepository->getByProductId($productId);
                if (isset($mappingData['id'])) {
                    if ($item->getFulfilledByAmazon() == 1) {
                        $orderItems['items'][$i]['quantity'] = floatval($item['qty_ordered']);
                        $orderItems['items'][$i]['sellerFulfillmentOrderItemId'] =
                        $order->getIncrementId().'-'.$item->getItemId();
                        $orderItems['items'][$i]['sellerSku'] = $mappingData['seller_sku'];
                        $orderItems['items'][$i]['giftMessage'] = "demo";
                        $orderItems['items'][$i]['displayableComment'] = 'Magento Item Id:'.$item->getItemId();
                        $orderItems['items'][$i]['perUnitDeclaredValue']['value'] = round($item['price_incl_tax'], 2);
                        $orderItems['items'][$i]['perUnitDeclaredValue']['currencyCode'] =
                        $order->getOrderCurrencyCode();
                        $mcfStatus[] = 1;
                        $i++;
                    } else {
                        $mcfStatus[] = 0;
                    }
                }
            }
        }
        $orderItems['mcfstatus'] = $mcfStatus;
        return $orderItems;
    }
    /**
     * Create order Request
     *
     * @param array $data
     * @return string
     */
    public function createOrder($data)
    {
        $serializeData = $this->jsonEncoder->encode($data);
        try {
            $urlAppend = 'createFulfillmentOrder.php';
            $requestStatus = '';
            $token=$this->helper->getConfig('token');
            $headers = ["Content-Type" => "application/json",
            'Authorization'=>$token];
            $url = $this->helper->getMidUrl().$urlAppend;
            $this->curl->get($url);
            $this->curl->setHeaders($headers);
            $this->curl->post($url, $serializeData);
            $response = $this->curl->getBody();
            $requestStatus = $this->curl->getStatus();
            if ($requestStatus != 404) {
                $response = $this->jsonDecoder->decode($response);
                return $response;
            } else {
                return $requestStatus;
            }
        } catch (\Exception $e) {
            return $e;
        }
    }
    /**
     * Auto Sync order Status
     *
     * @return array
     */
    public function autoOrderStatusSync()
    {
        $filterStatus = ['complete', 'canceled'];
        $shippingMethod = ['amazonshipping_standard', 'amazonshipping_expedited', 'amazonshipping_priority'];
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('status', $filterStatus, 'nin')
            ->addFilter('shipping_method', $shippingMethod, 'in')
            ->create();
        $orders = $this->orderRepository->getList($searchCriteria);
        $status = '';
        $urlAppend = 'getFulfillmentOrder.php';
        foreach ($orders as $item) {
            if ($item['amazon_order_id']) {
                $order_id = $item['entity_id'];
                $amcf_order_id = $item['amazon_order_id'];
                $requestStatus='';
                $param = ['order_id' => '000000011'];
                $token = $this->helper->getConfig('token');
                $headers = ["Content-Type" => "application/json",
                'Authorization' => $token];
                $url = $this->helper->getMidUrl().$urlAppend;
                $this->curl->post($url, $param);
                $this->curl->setHeaders($headers);
                $response = $this->curl->getBody();
                $requestStatus = $this->curl->getStatus();
                if ($requestStatus != 404) {
                    $response = $this->jsonDecoder->decode($response);
                    if (isset($response['payload']['fulfillmentOrder']['fulfillmentOrderStatus'])) {
                        $status = $response['payload']['fulfillmentOrder']['fulfillmentOrderStatus'];
                    }
                     
                }
                return $status;
            } else {
                return $requestStatus;
            }
        }
    }

    /**
     * Get order
     *
     * @param  array $order
     * @return array $orderItemStatus
     */
    public function getOrderItemsStatus($order)
    {
        $website = $this->storeManager->getStore()->getWebsiteId();
        $orderItemStatus = [];
        $mcfStatus = [];
        $mcfProducts = [];
        $orderItems =  $order->getAllItems();
        $shipmentStatus = $this->helper->getConfig('shipping_config_ship');
        $shipmentMethodStatus = $this->helper->getConfig('shipping_config_active');
        if ($this->helper->isConnected() && $shipmentMethodStatus) {
            foreach ($orderItems as $item) {
                $productId = $item->getProductId();
                $orderItemId = $item->getId();
                $skuSearchCriteria = $this->searchCriteriaBuilder
                ->addFilter('product_id', $productId, 'eq')
                ->addFilter('website', $website, 'eq')->create();
                $skuMappingData = $this->skuMappingRepository->getList($skuSearchCriteria);
                if ($skuMappingData->getTotalCount() > 0) {
                    foreach ($skuMappingData->getItems() as $data) {
                        if ($data['status'] && $data['sync_status']) {
                            $orderItemStatus['items'][] = ["order_item_id" => $orderItemId,
                            "product_id" => $productId, "mcf" => 1];
                            $orderItemStatus['mcf_products'][]= $productId;
                            $mcfStatus[] = 1;
                        } else {
                            $orderItemStatus['items'][] = ["order_item_id" => $orderItemId,
                            "product_id" => $productId, "mcf" => 0];
                            $mcfStatus[] = 0;
                        }
                    }
                } else {
                    $mcfStatus[] = 0;
                    $orderItemStatus['items'][] = [ "order_item_id" => $orderItemId,
                                                    "product_id" => $productId,
                                                    "mcf" => 0
                                                  ];
                }
            }
            $mcf = array_unique($mcfStatus);
            if (count($mcf) > 1) {
                if ($shipmentStatus && $this->helper->getConfig('shipping_config_exclusive')) {
                    $amazonMcfStatus = 2;
                } else {
                    $amazonMcfStatus = 0;
                }
            } else {
                if (strpos($order->getShippingMethod(), 'amazonshipping_') !== false) {
                    $amazonMcfStatus = $mcf[0];
                } else {
                    $amazonMcfStatus = 0;
                }
            }
            $orderItemStatus['amazon-mcf-status'] = $amazonMcfStatus;
        } else {
            $orderItemStatus['amazon-mcf-status'] = 0;
        }
        return $orderItemStatus;
    }

    /**
     * Create shipment in Magento for AMCF products
     *
     * @param int $orderId
     * @return string
     */
    public function createShipment($orderId)
    {
        $order = $this->orderRepository->get($orderId);
        // to check order can ship or not
        if (!$order->canShip()) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __("You can't create the shipment of this order.")
            );
        }
        $shipItems = [];
        if ($order->getFulfilledByAmazon() != 0) {
            $orderShipment = $this->convertOrder->toShipment($order);
            foreach ($order->getAllItems() as $orderItem) {
                if ($orderItem->getFulfilledByAmazon() == 1) {
                    if (!$orderItem->getQtyToShip() || $orderItem->getIsVirtual()) {
                        continue;
                    }
                    $qty = $orderItem->getQtyToShip();
                    $shipmentItem = $this->convertOrder->itemToShipmentItem($orderItem)->setQty($qty);
                    $orderShipment->addItem($shipmentItem);
                    $shipItems[$orderItem->getItemId()] = $qty;
                }
                $orderShipment->register();
                $orderShipment->getOrder()->setIsInProcess(true);
            }
            try {
                // Save created Order Shipment
                $orderShipment->save();
                $orderShipment->getOrder()->save();
                // Send Shipment Email
                $this->shipmentNotifier->notify($orderShipment);
                $orderShipment->save();
            } catch (\Exception $e) {
                throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()));
            }
        }
        return $shipItems;
    }
    /**
     * Get Amzon order Status
     *
     * @param string $incrementId
     * @return array
     */
    public function getAmcfOrderStatus($incrementId)
    {
    
        $amcf_order_id = $incrementId;
        $urlAppend = 'getFulfillmentOrder.php';
        $requestStatus='';
        $status = [];
        $param = ['order_id' => $incrementId];
        $serializeData = $this->jsonEncoder->encode($param);
        $token=$this->helper->getConfig('token');
        $headers = ["Content-Type" => "application/json",
        'Authorization' => $token];
        $url = $this->helper->getMidUrl().$urlAppend;
        $this->curl->get($url);
        $this->curl->setHeaders($headers);
        $this->curl->post($url, $serializeData);
        $response = $this->curl->getBody();
        $requestStatus = $this->curl->getStatus();
        $status = [];
        if ($requestStatus != 404) {
            $response = $this->jsonDecoder->decode($response);
            if (isset($response['payload']['fulfillmentOrder']['sellerFulfillmentOrderId'])) {
                $status['sellerFulfillmentOrderId'] =
                $response['payload']['fulfillmentOrder']['sellerFulfillmentOrderId'];
            }
            if (isset($response['payload']['fulfillmentOrder']['fulfillmentOrderStatus'])) {
                $status['fulfillmentOrderStatus'] = $response['payload']['fulfillmentOrder']['fulfillmentOrderStatus'];
            }
            if (isset($response['payload']['fulfillmentShipments'][0]['fulfillmentShipmentStatus'])) {
                $status['fulfillmentShipmentStatus'] =
                $response['payload']['fulfillmentShipments'][0]['fulfillmentShipmentStatus'];
            }
            if (isset($response['payload']['fulfillmentShipments'])) {
                $shipmentItem = $response['payload']['fulfillmentShipments'];
                $i=0;
                foreach ($shipmentItem as $shipItem) {
                    if (isset($shipItem['fulfillmentShipmentItem'][0]['packageNumber'])
                    && $shipItem['fulfillmentShipmentItem'][0]['packageNumber'] != 0) {
                        $status['shipmentData'][$i]['sellerFulfillmentOrderItemId'] =
                        $shipItem['fulfillmentShipmentItem'][0]['sellerFulfillmentOrderItemId'];
                        $status['shipmentData'][$i]['packageNumber'] =
                        $shipItem['fulfillmentShipmentItem'][0]['packageNumber'];
                        $i++;
                    }
                }
                $status['fulfillmentOrderItemStatus'] =
                $response['payload']['fulfillmentOrder']['fulfillmentOrderStatus'];
            }
            if (isset($response['payload']['fulfillmentOrderItems'])) {
                $sellerFulfillmentOrderItemId = $response['payload']['fulfillmentOrderItems'];
                $i = 0;
                foreach ($sellerFulfillmentOrderItemId as $orderItem) {
                    if (isset($orderItem['sellerFulfillmentOrderItemId'])) {
                        $status['itemData'][$i]['sellerFulfillmentOrderItemId'] =
                        $orderItem['sellerFulfillmentOrderItemId'];
                        $i++;
                    }
                }
            }
            return $status;
        } else {
            return $requestStatus;
        }
    }

    /**
     * Get Amazon tracking details
     *
     * @param string $packageNumber
     * @return array
     */
    public function getPreviewTrackingDetails($packageNumber)
    {
        $urlAppend = 'getPreviewTrackingDetails.php';
        $requestStatus = '';
        $tracking = [];
        $param = ['packageNumber' => $packageNumber];
        $serializeData = $this->jsonEncoder->encode($param);
        $token = $this->helper->getConfig('token');
        $headers = ["Content-Type" => "application/json",
        'Authorization'=>$token];
        $url=$this->helper->getMidUrl().$urlAppend;
        $this->curl->get($url);
        $this->curl->setHeaders($headers);
        $this->curl->post($url, $serializeData);
        $response = $this->curl->getBody();
        $requestStatus = $this->curl->getStatus();
        if ($requestStatus != 404) {
            $response = $this->jsonDecoder->decode($response);
            if (isset($response['payload'])) {
                $responseData = $response['payload'];
                $data =['packageNumber','trackingNumber','carrierCode','carrierURL'];
                foreach ($data as $single) {
                    $tracking[$single] =  $responseData[$single];
                }
            }
            return $tracking;
        } else {
            return $requestStatus;
        }
    }
}
