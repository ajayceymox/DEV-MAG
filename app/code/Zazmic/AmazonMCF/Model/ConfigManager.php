<?php declare(strict_types=1);
/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */
namespace Zazmic\AmazonMCF\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Zazmic\AmazonMCF\Api\ConfigInterface;
use Magento\Directory\Api\CountryInformationAcquirerInterface;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Json\Encoder;
use Magento\Framework\Json\Decoder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Zazmic\AmazonMCF\Api\SkuMappingRepositoryInterface;
use Zazmic\AmazonMCF\Api\Data\SkuMappingInterface;
use Zazmic\AmazonMCF\Api\Data\SkuMappingInterfaceFactory;
use Zazmic\AmazonMCF\Api\InventorySyncinfoRepositoryInterface;
use Zazmic\AmazonMCF\Api\Data\InventorySyncinfoInterface;
use Zazmic\AmazonMCF\Api\Data\InventorySyncinfoInterfaceFactory;
use Zazmic\AmazonMCF\Api\SyncinfoRepositoryInterface;
use Zazmic\AmazonMCF\Api\Data\SyncinfoInterface;
use Zazmic\AmazonMCF\Api\Data\SyncinfoInterfaceFactory;
use Zazmic\AmazonMCF\Helper\Data;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Catalog\Model\Product\Action as ProductAction;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\ResourceModel\Order;
use Magento\Framework\App\RequestInterface;
use Zazmic\AmazonMCF\Model\FulfillmentOrderManager;
use Magento\Sales\Api\Data\ShipmentTrackInterfaceFactory;
use Magento\Sales\Api\ShipmentItemRepositoryInterface;
use Magento\Sales\Api\ShipmentRepositoryInterface;
use Magento\Sales\Api\OrderItemRepositoryInterface;
use Zazmic\AmazonMCF\Model\McfLogManager;
use Magento\Framework\Stdlib\DateTime\DateTime;

class ConfigManager implements ConfigInterface
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
     * @var CountryInformationAcquirerInterface
     */
    private $countryInformationInterface;
    /**
     * @var Curl
     */
    private $curlClient;
    /**
     * @var Encoder
     */
    private $jsonEncoder;
    /**
     * @var Json
     */
    private $json;
    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;
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
     * @var ProductRepositoryInterface
     */
    private $productRepository;
    /**
     * @var StockRegistryInterface
     */
    private $stockRegistry;
    /**
     * @var WriterInterface
     */
    private $configWriter;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var TimezoneInterface
     */
    private $date;
    /**
     * @var InventorySyncinfoRepositoryInterface
     */
    private $inventorySyncinfoRepository;
    /**
     * @var InventorySyncinfoInterfaceFactory
     */
    private $inventorySyncinfoInterfaceFactory;
    /**
     * @var SyncinfoRepositoryInterface
     */
    private $syncinfoRepository;
    /**
     * @var SyncinfoInterfaceFactory
     */
    private $syncinfoInterfaceFactory;
    /**
     * @var ProductAction
     */
    private $productAction;
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;
    /**
     * @var Order
     */
    private $orderResourceModel;
    /**
     * @var RequestInterface
     */
    private $request;
    /**
     * @var FulfillmentOrderManager
     */
    private $fulfillmentOrderManager;
    /**
     * @var ShipmentRepositoryInterface
     */
    private $shipmentRepository;
    /**
     * @var ShipmentTrackInterfaceFactory
     */
    private $trackFactory;
    /**
     * @var ShipmentItemRepositoryInterface
     */
    private $shipmentItemRepository;
    /**
     * @var OrderItemRepositoryInterface
     */
    private $orderItem;
    /**
     * @var McfLogManager
     */
    private $mcfLogManager;
    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param Data $helper
     * @param CountryInformationAcquirerInterface $countryInformationInterface
     * @param Curl $curlClient
     * @param Encoder $jsonEncoder
     * @param Decoder $jsonDecoder
     * @param Json $json
     * @param DataObjectHelper $dataObjectHelper
     * @param SkuMappingRepositoryInterface $skuMappingRepository
     * @param SkuMappingInterfaceFactory $skuMappingInterfaceFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param ProductRepositoryInterface $productRepository
     * @param StockRegistryInterface $stockRegistry
     * @param WriterInterface $configWriter
     * @param StoreManagerInterface $storeManager
     * @param InventorySyncinfoRepositoryInterface $inventorySyncinfoRepository
     * @param InventorySyncinfoInterfaceFactory $inventorySyncinfoInterfaceFactory
     * @param SyncinfoRepositoryInterface $syncinfoRepository
     * @param SyncinfoInterfaceFactory $syncinfoInterfaceFactory
     * @param TimezoneInterface $date
     * @param ProductAction $action
     * @param OrderRepositoryInterface $orderRepository
     * @param Order $orderResourceModel
     * @param RequestInterface $request
     * @param FulfillmentOrderManager $fulfillmentOrderManager
     * @param ShipmentRepositoryInterface $shipmentRepository
     * @param ShipmentItemRepositoryInterface $shipmentItemRepository
     * @param ShipmentTrackInterfaceFactory $trackFactory
     * @param OrderItemRepositoryInterface $orderItem
     * @param McfLogManager $mcfLogManager
     * @param DateTime $dateTime
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Data $helper,
        CountryInformationAcquirerInterface $countryInformationInterface,
        Curl $curlClient,
        Encoder $jsonEncoder,
        Decoder $jsonDecoder,
        Json $json,
        DataObjectHelper $dataObjectHelper,
        SkuMappingRepositoryInterface $skuMappingRepository,
        SkuMappingInterfaceFactory $skuMappingInterfaceFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ProductRepositoryInterface $productRepository,
        StockRegistryInterface $stockRegistry,
        WriterInterface $configWriter,
        StoreManagerInterface $storeManager,
        InventorySyncinfoRepositoryInterface $inventorySyncinfoRepository,
        InventorySyncinfoInterfaceFactory $inventorySyncinfoInterfaceFactory,
        SyncinfoRepositoryInterface $syncinfoRepository,
        SyncinfoInterfaceFactory $syncinfoInterfaceFactory,
        TimezoneInterface $date,
        ProductAction $action,
        OrderRepositoryInterface $orderRepository,
        Order $orderResourceModel,
        RequestInterface $request,
        FulfillmentOrderManager $fulfillmentOrderManager,
        ShipmentRepositoryInterface $shipmentRepository,
        ShipmentItemRepositoryInterface $shipmentItemRepository,
        ShipmentTrackInterfaceFactory $trackFactory,
        OrderItemRepositoryInterface $orderItem,
        McfLogManager $mcfLogManager,
        DateTime $dateTime
    ) {
        $this->helper = $helper;
        $this->scopeConfig = $scopeConfig;
        $this->countryInformationInterface = $countryInformationInterface;
        $this->curl = $curlClient;
        $this->jsonEncoder = $jsonEncoder;
        $this->jsonDecoder = $jsonDecoder;
        $this->json = $json;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->skuMappingRepository = $skuMappingRepository;
        $this->skuMappingInterfaceFactory = $skuMappingInterfaceFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->productRepository = $productRepository;
        $this->stockRegistry = $stockRegistry;
        $this->configWriter = $configWriter;
        $this->storeManager = $storeManager;
        $this->inventorySyncinfoRepository = $inventorySyncinfoRepository;
        $this->inventorySyncinfoInterfaceFactory = $inventorySyncinfoInterfaceFactory;
        $this->syncinfoRepository = $syncinfoRepository;
        $this->syncinfoInterfaceFactory = $syncinfoInterfaceFactory;
        $this->date = $date;
        $this->productAction = $action;
        $this->orderRepository = $orderRepository;
        $this->orderResourceModel = $orderResourceModel;
        $this->request =$request;
        $this->fulfillmentOrderManager = $fulfillmentOrderManager;
        $this->shipmentRepository = $shipmentRepository;
        $this->shipmentItemRepository = $shipmentItemRepository;
        $this->trackFactory = $trackFactory;
        $this->orderItemRepo = $orderItem;
        $this->mcfLogManager = $mcfLogManager;
        $this->dateTime = $dateTime;
    }

    /**
     * IsEnabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return true;
    }

    /**
     * Check connected
     *
     * @return bool
     */
    public function isConnected()
    {
        return $this->helper->isConnected();
    }
    
    /**
     * Get connected Country
     *
     * @return bool
     */
    public function getConfigCountry()
    {
        $countryName = null;
        $countryCode = $this->getConfig('country');
        $config_data = $this->countryInformationInterface->getCountryInfo($countryCode);
        $countryName = $config_data->getFullNameLocale();
        return $countryName;
    }

    /**
     * Get Config Paths
     *
     * @param string $configPath
     * @param string $websiteId
     * @return bool
     */
    public function getConfig($configPath, $websiteId = null)
    {
        $data = $this->helper->getConfigPathVariables();
        $configValue = $this->scopeConfig->getValue(
            $data[$configPath],
            ScopeInterface::SCOPE_WEBSITES
        );
        if ($websiteId) {
            $configValue = $this->scopeConfig->getValue(
                $data[$configPath],
                ScopeInterface::SCOPE_WEBSITES,
                $websiteId
            );
        }
        return $configValue;
    }
    /**
     * Get Config Paths
     *
     * @param string $configPath
     * @param int $scopeId
     * @return bool
     */
    public function getConfigFromScope($configPath, $scopeId)
    {
        $data = $this->helper->getConfigPathVariables();
        $configValue = $this->scopeConfig->getValue($data[$configPath], ScopeInterface::SCOPE_WEBSITES, $scopeId);
        return $configValue;
    }

    /**
     * Get MCF Request
     *
     * @param string $requestType
     * @param array $data
     * @param int $websiteId
     * @return string
     */
    public function getMcfRequest($requestType, $data = null, $websiteId = null)
    {
        if ($requestType == 'catalog') {
            $urlAppend = 'catalog.php';
        }
        if ($requestType == 'inventory') {
            $urlAppend = 'inventory.php';
        }
        if ($requestType == 'disconnect') {
            $urlAppend = 'disconnect.php';
        }
        try {
            // get method
            $requestStatus = '';
            if (is_array($data) && count($data) > 0) {
                $serializeData = $this->jsonEncoder->encode($data);
                $params = $serializeData;
            } else {
                $params = null;
            }
            $token = $this->getConfig('token');
            if ($websiteId) {
                $token = $this->getConfig('token', $websiteId);
            }
            $headers = ["Content-Type" => "application/json",
            'Authorization'=>$token];
            $url = $this->helper->getMidUrl().$urlAppend;
            $this->curl->get($url);
            $this->curl->setHeaders($headers);
            $this->curl->post($url, $params);
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
     * Auto and manual mapping
     *
     * @param array $mapArr
     * @return string
     */
    public function skuMap($mapArr)
    {
        try {
            $date = $this->dateTime->gmtDate();            
            $activeWebsites = $this->helper->getActiveConnections();
            foreach ($activeWebsites as $activeWebsite) {
                if ($this->helper->isConnectedExist($activeWebsite[0], 'spapi_oauth_code')) {
                    $sellerSku = $mapArr['seller_sku'];
                    $product = $this->productRepository->get($sellerSku);
                    if ($product && in_array($activeWebsite[0], $product->getWebsiteIds())) {
                        $searchCriteria = $this->searchCriteriaBuilder
                                        ->addFilter('product_id', $product->getId(), 'eq')
                                        ->addFilter('website', $activeWebsite[0], 'eq')->create();
                        $skuData = $this->skuMappingRepository->getList($searchCriteria);
                        if (count($skuData->getItems()) > 0) {
                            foreach ($skuData->getItems() as $value) {
                                $skuMapId = $value['id'];
                                $skuMapProductId = $value['product_id'];
                                $productSku = $value['sku'];
                            }
                            if ($product->getId() != $skuMapProductId) {
                                $result = ['status'=>'error','msg'=> $productSku];
                            }
                            $rawData = $this->skuMappingRepository->getById($skuMapId);
                            $rawData->setProductId($product->getId());
                            $rawData->setAsin($mapArr['asin']);
                            $rawData->setSellerSku($mapArr['seller_sku']);
                            $rawData->setAmazonProductName($mapArr['product_name']);
                            $rawData->setWebsite($activeWebsite[0]);
                            $rawData->setStatus('1');
                            $rawData->setSyncStatus('1');
                            $rawData->setUpdatedAt($date);
                            $result = $this->skuMappingRepository->save($rawData);
                            $result = ['status'=>'updated'];
                        } else {
                            $data = [
                                'product_id' => $product->getId(),
                                'sku' => $product->getSku(),
                                'seller_sku' => $mapArr['seller_sku'],
                                'asin' => $mapArr['asin'],
                                'product_name' => $product->getName(),
                                'amazon_product_name' => $mapArr['product_name'],
                                'sync_status' => '1',
                                'website' => $activeWebsite[0],
                                'status' => '1',
                            ];
                            $collection = $this->skuMappingInterfaceFactory->create();
                            $this->dataObjectHelper->populateWithArray(
                                $collection,
                                $data,
                                skuMappingInterface::class
                            );
                            $this->skuMappingRepository->save($collection);
                            $result = ['status'=>'saved'];
                        }
                        $this->updateProduct($product->getId(), '1', $activeWebsite[0]);
                       
                    }
                }
            }
            return $result;
        } catch (\RuntimeException $e) {
            return false;
        } catch (\Exception $e) {
            return false;
        } catch (LocalizedException $e) {
            return false;
        }
    }
    /**
     * Get MapStatus and Manual Mapping
     *
     * @param string $requestType
     * @param string $sellerSku
     * @param int $webSiteId
     * @return string
     */
    public function getMcfMapStatus($requestType, $sellerSku, $webSiteId)
    {
        try {
            $response = $this->getMcfRequest($requestType, null, $webSiteId);
            if ($response != 404 && isset($response['payload'])) {
                $data = [];
                foreach ($response['payload']['inventorySummaries'] as $item) {
                    if ($item['sellerSku'] == $sellerSku) {
                         $data['seller_sku'] = $item['sellerSku'];
                         $data['asin'] = $item['asin'];
                         $data['productName'] = $item['productName'];
                    }
                }
                if (!empty($data)) {
                    return $data;
                } else {
                    return false;
                }
            } else {
                 return false;
            }
        } catch (\Exception $e) {
            return $e;
        }
    }

    /**
     * Update Inventory
     *
     * @param string $sellerSku
     * @param int $stock
     * @return string
     */
    public function updateInventory($sellerSku, $stock)
    {
        try {
            $searchCriteria = $this->searchCriteriaBuilder->addFilter('seller_sku', $sellerSku, 'eq')->create();
            $skuData = $this->skuMappingRepository->getList($searchCriteria);
            if (count($skuData->getItems()) > 0) {
                foreach ($skuData->getItems() as $value) {
                    $sku = $value['sku'];
                    $productId = $value['id'];
                }
                $stockItem = $this->stockRegistry->getStockItemBySku($sku);
                $prevQty = $stockItem->getQty();
                $stockItem->setQty($stock);
                $stockItem->setIsInStock((bool)$stock);
                if ($this->stockRegistry->updateStockItemBySku($sku, $stockItem)) {
                    $data = [
                        'product_id' => $productId,
                        'sku' => $sku,
                        'prev_qty' => $prevQty,
                        'updated_qty' => $stock,
                    ];
                    $collection = $this->inventorySyncinfoInterfaceFactory->create();
                    $this->dataObjectHelper->populateWithArray(
                        $collection,
                        $data,
                        InventorySyncinfoInterface::class
                    );
                    $result = $this->inventorySyncinfoRepository->save($collection);
                    return true;
                } else {
                    return false;
                }
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Sycn Inventory
     *
     * @param string $user
     * @param int $websiteId
     * @return array
     */
    public function inventorySync($websiteId, $user = '')
    {
        $syncSkus = [];
        try {
            $mcfItems = $this->getMcfRequest('inventory');
            if ($mcfItems != 404 && isset($mcfItems['payload'])) {
                $i = 0;
                foreach ($mcfItems['payload']['inventorySummaries'] as $data) {
                    $stock = '';
                    $sellerSku = '';
                    $sellerSku = $data['sellerSku'];
                    $stock = $data['inventoryDetails']['fulfillableQuantity'];
                    $result = $this->updateInventory($sellerSku, $stock);
                    if ($result) {
                        $i++;
                        $syncSkus[] = $sellerSku;
                    }
                }
                $this->syncInfoUpdate('inventory', $i);
                    $this->configUpdate(
                        'zazmic/inventory_config/last_synced',
                        $this->date->date()->format('Y-m-d H:i:s'),
                        ScopeInterface::SCOPE_WEBSITES,
                        $websiteId
                    );
                $result = ['success' => true,'msg' =>__("Synced %1 inventory.", $i)];
                if ($i > 0) {
                    $msg= 'Synced '.$i.' inventory with seller SKU(s) - "'.implode(",", $syncSkus).'"';
                } else {
                    $msg= 'Synced '.$i.' inventory';
                }
                $logData = [
                    'user' => $user,
                    'area' => 'Inventory sync',
                    'type' => 'Info',
                    'details' => $msg
                ];
            } else {
                $logData = [
                    'user' => $user,
                    'area' => 'Inventory sync',
                    'type' => 'Error',
                    'details' => 'Failed to sync inventory due to invalid server response',
                ];
                $result = ['error' => true,'msg' =>__("Failed to sync inventory due to invalid server response")];
            }
        } catch (\Exception $e) {
            $logData = [
                'user' => $user,
                'area' => 'Inventory sync',
                'type' => 'Error',
                'details' => $e->getMessage(),
            ];
            $result = ['error' => true,'msg' =>__("Failed to sync inventory %1.", $e->getMessage())];
        }
        if (isset($logData)) {
            $this->mcfLogManager->addLog($logData);
        }
        return $result;
    }
    
    /**
     * Auto Sync Inventory
     *
     * @return array
     */
    public function autoInventorySync()
    {
        try {
            $websites = $this->helper->getActiveConnections();

            foreach ($websites as $website) {
                if ($this->helper->isConnectedExist($website[0], 'spapi_oauth_code')) {
                    $configValue = $this->getConfig('auto_inventory', $website[0]);
                    $lastSync = $this->getConfig('last_synced', $website[0]);
                    $inventorySyncInterval = $this->getConfig('inventory_sync_interval', $website[0]);
                    $currentTime=$this->date->date()->format('Y-m-d H:i:s');
                    $d1 = strtotime($currentTime);
                    $d2 = $lastSync!= '' ? strtotime($lastSync) : 0;
                    $interval = abs($d1 - $d2);
                    $diff = round($interval / 60);
                    if ($configValue=='1' && $diff >= $inventorySyncInterval) {
                        $this->inventorySync($website[0],'Cron');
                    }
                }
            }
        } catch (Exception $e) {
            return false;
        }
    }
    /**
     * Sync InfoUpdate
     *
     * @param string $action
     * @param int $items
     * @return string
     */
    public function syncInfoUpdate($action, $items)
    {
        $data = [
            'sync_action' => $action,
            'sync_items_count' => $items,
        ];
        $collection = $this->syncinfoInterfaceFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $collection,
            $data,
            SyncinfoInterface::class
        );
        $invResult = $this->syncinfoRepository->save($collection);
    }
    /**
     * Auto Sync order Status
     *
     * @return array
     */
    public function autoOrderStatusSync()
    {
        try {
            $websites = $this->helper->getActiveConnections();
            foreach ($websites as $website) {
                if ($this->helper->isConnectedExist($website[0], 'spapi_oauth_code')) {
                    $configValue = $this->getConfig('shipment_sync_status', $website[0]);
                    $lastSynced = $this->getConfig('last_synced', $website[0]);
                    $orderSyncInterval = $this->getConfig('shipment_sync_interval', $website[0]);
                    $currentTime=$this->date->date()->format('Y-m-d H:i:s');
                    $d1 = strtotime($currentTime);
                    $d2 = $lastSynced!= '' ? strtotime($lastSynced) : 0;
                    $interval  = abs($d1 - $d2);
                    $diff   = round($interval / 60);
                    if ($configValue=='1' && $diff >= $orderSyncInterval) {
                        $this->configWriter->save(
                            'zazmic/shipmentstatus/last_synced',
                            $this->date->date()->format('Y-m-d H:i:s'),
                            ScopeInterface::SCOPE_WEBSITES,
                            $website[0]
                        );
                        $this->orderStatusUpdate();
                    }
                }
            }
        } catch (Exception $e) {
            return false;
        }
    }
    /**
     * UpdateProduct
     *
     * @param int $productId
     * @param int $attributeValue
     * @param int $webSiteId
     */
    public function updateProduct($productId, $attributeValue, $webSiteId)
    {
        $this->productAction->updateAttributes([$productId], ['mcf_enabled' => $attributeValue], $webSiteId);
    }
    /**
     * Get  Country Name from countryCode
     *
     * @param string $countryCode
     * @return string
     */
    public function getConfigCountryName($countryCode)
    {
        $countryName = null;
        $config_data = $this->countryInformationInterface->getCountryInfo($countryCode);
        $countryName = $config_data->getFullNameLocale();
        return $countryName;
    }
    /**
     * OrderStatusUpdate
     *
     * @return string
     */
    public function orderStatusUpdate()
    {
        $syncOrders = [];
        try {
            $filterStatus = ['complete', 'canceled'];
            $searchCriteria = $this->searchCriteriaBuilder
                             ->addFilter('status', $filterStatus, 'nin')
                             ->create();
            $orderCollection = $this->orderRepository->getList($searchCriteria);
            foreach ($orderCollection as $item) {
                if ($item['fulfilled_by_amazon'] == '1' || $item['fulfilled_by_amazon'] == '2') {
                    $incrementId = $item['increment_id'];
                    $orderId = $item['entity_id'];
                    $status = $this->fulfillmentOrderManager->getAmcfOrderStatus($incrementId);
                    if ($status != 404 && count($status) > 0) {
                        $order = $this->orderRepository->get($orderId);
                        if (isset($status['fulfillmentOrderStatus'])) {
                            $fulfillmentOrderStatus = $status['fulfillmentOrderStatus'];
                            $order->setAmazonOrderStatus($fulfillmentOrderStatus);
                        } else {
                            $fulfillmentShipmentStatus = $item->getAmazonOrderStatus();
                        }
                        if ($item['fulfilled_by_amazon'] == '1') {
                            $this->mcfOrderStatusUpdates($orderId, $fulfillmentOrderStatus);
                        }
                        if (isset($status['fulfillmentShipmentStatus'])) {
                            $fulfillmentShipmentStatus = $status['fulfillmentShipmentStatus'];
                            $order->setAmazonShipmentStatus($fulfillmentShipmentStatus);
                        } else {
                            $fulfillmentShipmentStatus = $item->getFulfillmentShipmentStatus();
                        }
                        $sellerFulfillmentOrderId = '';
                        if (isset($status['sellerFulfillmentOrderId'])) {
                            $sellerFulfillmentOrderId = $status['sellerFulfillmentOrderId'];
                        }
                        $this->orderRepository->save($order);
                        $orderItems = $order->getAllItems();
                        foreach ($orderItems as $orderItem) {
                            if (isset($status['itemData'])) {
                                foreach ($status['itemData'] as $itemData) {
                                    if (isset($itemData['sellerFulfillmentOrderItemId'])) {
                                        $itemId = $itemData['sellerFulfillmentOrderItemId'];
                                        if ($itemId == $orderItem->getSellerFulfillmentOrderItemId()) {
                                            $itemRepo = $this->orderItemRepo->get($orderItem->getId());
                                            $itemRepo->setAmazonOrderStatus($fulfillmentOrderStatus);
                                            $itemRepo->setAmazonShipmentStatus($fulfillmentShipmentStatus);
                                            $itemRepo->save();
                                        }
                                    }
                                }
                            }
                            if (isset($status['shipmentData'])) {
                                foreach ($status['shipmentData'] as $shipmentData) {
                                    if ($shipmentData['sellerFulfillmentOrderItemId'] ==
                                    $orderItem->getSellerFulfillmentOrderItemId()) {
                                         $itemRepo = $this->orderItemRepo->get($orderItem->getId());
                                        $itemRepo->setAmazonPackageNumber($shipmentData['packageNumber']);
                                        $itemRepo->save();
                                        $this->updateShippmentDetails($orderId, $orderItem->getId());
                                    }
                                }
                            }
                        }
                    }
                    $syncOrders[] = $incrementId;
                }
            }
            $logData = [
                'user' => 'Cron Job',
                'area' => 'Order sync',
                'type' => 'Info',
                'details' => 'MCF orders with order id(s) '.implode(",", $syncOrders).' are synced successfully',
            ];
            if (isset($logData)) {
                $this->mcfLogManager->addLog($logData);
            }
            return true;
        } catch (Exception $e) {
            $logData = [
                'user' => 'Cron Job',
                'area' => 'Order sync',
                'type' => 'Error',
                'details' => $e->getMessage(),
            ];
            if (isset($logData)) {
                $this->mcfLogManager->addLog($logData);
            }
            return false;
        }
    }
    /**
     * McfOrderStatusUpdates
     *
     * @param int $orderId
     * @param string $status
     * @return string
     */
    public function mcfOrderStatusUpdates($orderId, $status)
    {
        $order = $this->orderRepository->get($orderId);
        if ($status == 'Cancelled') {
            $order->setState(\Magento\Sales\Model\Order::STATE_CANCELED);
            $order->setStatus(\Magento\Sales\Model\Order::STATE_CANCELED);
        } elseif ($status == 'Complete') {
            $order->setState(\Magento\Sales\Model\Order::STATE_COMPLETE);
            $order->setStatus(\Magento\Sales\Model\Order::STATE_COMPLETE);
        } else {
            $order->setState(\Magento\Sales\Model\Order::STATE_PROCESSING);
            $order->setStatus(\Magento\Sales\Model\Order::STATE_PROCESSING);
        }
        if ($this->orderRepository->save($order)) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * UpdateShippmentDetails
     *
     * @param int $orderId
     * @param int $orderItemId
     * @return string
     */
    public function updateShippmentDetails($orderId, $orderItemId)
    {
        $order = $this->orderRepository->get($orderId);
        if ($order->getAmazonShipmentStatus()!='') {
            $searchCriteria = $this->searchCriteriaBuilder->addFilter('order_item_id', $orderItemId)->create();
            $shipment = $this->shipmentItemRepository->getList($searchCriteria);
            if (count($shipment->getItems()) > 0) {
                $shipmentItems = $shipment->getItems();
                $carrier = 'custom';
                $title = 'Amazon Fulfillment';
                $shippmentData = $order->getShipmentsCollection();
                $itemCollection = $this->orderItemRepo->get($orderItemId);
                $number = $itemCollection->getAmazonPackageNumber();
                foreach ($shipmentItems as $shipItem) {
                    $trackModel = $this->trackFactory->create();
                    $trackModel->load($number, 'track_number');
                    if ($trackModel->getId() == '') {
                        $shipment = $this->shipmentRepository->get($shipItem->getParentId());
                        $track = $this->trackFactory->create()
                        ->setNumber($number)
                        ->setCarrierCode($carrier)
                        ->setTitle($title);
                        $shipment->addTrack($track);
                        $this->shipmentRepository->save($shipment);
                    }
                }
            }
        }
    }
    /**
     * Manual Order Sync
     *
     * @param int $orderId
     */
    public function manualOrderSync($orderId)
    {
        try {
            $order = $this->orderRepository->get($orderId);
            $incrementId = $order->getIncrementId();
            $status = $this->fulfillmentOrderManager->getAmcfOrderStatus($incrementId);
            if ($status != 404 && count($status) > 0) {
                if (isset($status['fulfillmentOrderStatus'])) {
                    $fulfillmentOrderStatus = $status['fulfillmentOrderStatus'];
                    $order->setAmazonOrderStatus($fulfillmentOrderStatus);
                } else {
                    $fulfillmentShipmentStatus = $order->getAmazonOrderStatus();
                }
                if ($order->getFulfilledByAmazon() == '1') {
                    $this->mcfOrderStatusUpdates($orderId, $fulfillmentOrderStatus);
                }
                if (isset($status['fulfillmentShipmentStatus'])) {
                    $fulfillmentShipmentStatus = $status['fulfillmentShipmentStatus'];
                    $order->setAmazonShipmentStatus($fulfillmentShipmentStatus);
                } else {
                    $fulfillmentShipmentStatus = $order->getFulfillmentShipmentStatus();
                }
                            $sellerFulfillmentOrderId = '';
                if (isset($status['sellerFulfillmentOrderId'])) {
                    $sellerFulfillmentOrderId = $status['sellerFulfillmentOrderId'];
                }
                $this->orderRepository->save($order);
                $orderItems = $order->getAllItems();
                foreach ($orderItems as $orderItem) {
                    if (isset($status['itemData'])) {
                        foreach ($status['itemData'] as $itemData) {
                            if (isset($itemData['sellerFulfillmentOrderItemId'])) {
                                $itemId = $itemData['sellerFulfillmentOrderItemId'];
                                if ($itemId == $orderItem->getSellerFulfillmentOrderItemId()) {
                                    $itemRepo = $this->orderItemRepo->get($orderItem->getId());
                                    $itemRepo->setAmazonOrderStatus($fulfillmentOrderStatus);
                                    $itemRepo->setAmazonShipmentStatus($fulfillmentShipmentStatus);
                                    $itemRepo->save();
                                }
                            }
                        }
                    }
                    if (isset($status['shipmentData'])) {
                        foreach ($status['shipmentData'] as $shipmentData) {
                            if ($shipmentData['sellerFulfillmentOrderItemId'] ==
                            $orderItem->getSellerFulfillmentOrderItemId()) {
                                $itemRepo = $this->orderItemRepo->get($orderItem->getId());
                                $itemRepo->setAmazonPackageNumber($shipmentData['packageNumber']);
                                $itemRepo->save();
                                $this->updateShippmentDetails($orderId, $orderItem->getId());
                            }
                        }
                    }
                }
                $logData = [
                    'area' => 'Manual order sync',
                    'type' => 'Info',
                    'details' => 'Order with id #'.$incrementId.' is synced successfully',
                ];
            }
        } catch (Exception $e) {
            $logData = [
                'area' => 'Manual order sync',
                'type' => 'Error',
                'details' => $e->getMessage(),
            ];
        }
        if (isset($logData)) {
            $this->mcfLogManager->addLog($logData);
        }
    }
    /**
     * Update Configuration Settings
     *
     * @param string $configPath
     * @param string $value
     * @param string $scope
     * @param int $scopeId
     */
    public function configUpdate($configPath, $value, $scope, $scopeId)
    {
        $this->configWriter->save(
            $configPath,
            $value,
            $scope,
            $scopeId
        );
    }
}
