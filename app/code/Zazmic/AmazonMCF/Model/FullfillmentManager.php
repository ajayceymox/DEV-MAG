<?php declare(strict_types=1);
/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */
namespace Zazmic\AmazonMCF\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Checkout\Model\Cart;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Json\Encoder;
use Magento\Framework\Json\Decoder;
use Magento\Directory\Model\RegionFactory;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Zazmic\AmazonMCF\Api\FullfillmentInterface;
use Zazmic\AmazonMCF\Api\SkuMappingRepositoryInterface;
use Zazmic\AmazonMCF\Helper\Data;

class FullfillmentManager implements FullfillmentInterface
{

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;
    /**
     * @var CheckoutSession
     */
    private $checkoutSession;
    /**
     * @var Cart
     */
    private $cart;
    /**
     * @var CartRepositoryInterface
     */
    private $quoteRepository;
    /**
     * @var SerializerInterface
     */
    private $serializer;
    /**
     * @var Curl
     */
    private $curlClient;
    /**
     * @var Encoder
     */
    private $jsonEncoder;
    /**
     * @var Decoder
     */
    private $jsonDecoder;
    /**
     * @var SkuMappingRepositoryInterface
     */
    private $skuMappingRepository;
    /**
     * @var TimezoneInterface
     */
    private $date;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var RegionFactory
     */
    private $regionFactory;
    /**
     * @var Data
     */
    private $helper;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param CheckoutSession $checkoutSession
     * @param Cart $cart
     * @param CartRepositoryInterface $quoteRepository
     * @param SerializerInterface $serializer
     * @param Curl $curlClient
     * @param Encoder $jsonEncoder
     * @param Decoder $jsonDecoder
     * @param SkuMappingRepositoryInterface $skuMappingRepository
     * @param TimezoneInterface $date
     * @param StoreManagerInterface $storeManager
     * @param RegionFactory $regionFactory
     * @param Data $helper
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        CheckoutSession $checkoutSession,
        Cart $cart,
        CartRepositoryInterface $quoteRepository,
        SerializerInterface $serializer,
        Curl $curlClient,
        Encoder $jsonEncoder,
        Decoder $jsonDecoder,
        SkuMappingRepositoryInterface $skuMappingRepository,
        TimezoneInterface $date,
        StoreManagerInterface $storeManager,
        RegionFactory $regionFactory,
        Data $helper,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->checkoutSession = $checkoutSession;
        $this->cart = $cart;
        $this->quoteRepository = $quoteRepository;
        $this->serializer = $serializer;
        $this->curl = $curlClient;
        $this->jsonEncoder = $jsonEncoder;
        $this->jsonDecoder = $jsonDecoder;
        $this->skuMappingRepository = $skuMappingRepository;
        $this->regionFactory = $regionFactory;
        $this->date = $date;
        $this->storeManager = $storeManager;
        $this->helper = $helper;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }
    /**
     * IsEnabled
     *
     * @return bool
     */
    public function isMcfEnabled()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }
    /**
     * Auto and manual mapping
     *
     * @param array $mapArr
     * @return string
     */
    public function getfullfillmentShipmentRates()
    {
        try {
            $quote = $this->cart->getQuote();
            $quoteId = $quote->getId();
            $shippingAddress = $quote->getShippingAddress();
            $street = $shippingAddress->getStreet();
            $street1 = isset($street[1]) ? $street[1] : '';
            $street2 = isset($street[2]) ? $street[2] : '';
            $requstArr = [];
            $address = [];
            if (!empty($shippingAddress->getFirstname())) {
                $address["name"] = $shippingAddress->getFirstname().' '.$shippingAddress->getLastname();
            }
            if (!empty($street[0])) {
                $address["addressLine1"] = $street[0].''.$street1.''.$street2;
            }
            if (!empty($shippingAddress->getCity())) {
                $address["city"] = $shippingAddress->getCity();
            }
            if (!empty($this->regionCode($shippingAddress->getRegionId()))) {
                $address["stateOrRegion"] = $this->regionCode($shippingAddress->getRegionId());
            }
            if (!empty($shippingAddress->getPostcode())) {
                $address["postalCode"] = $shippingAddress->getPostcode();
            }
            if (!empty($shippingAddress->getCountry())) {
                $address["countryCode"] = $shippingAddress->getCountry();
            }
            if (!empty($shippingAddress->getTelephone())) {
                $address["phone"] = $shippingAddress->getTelephone();
            }
            foreach ($quote->getAllVisibleItems() as $item) {
                $skuMap = $this->skuMappingRepository->getByProductId($item->getProductId());
                if ($skuMap['seller_sku'] != '' &&
                    $skuMap['sync_status'] == 1 &&
                    $skuMap['status'] == 1) {
                    $items[] = [
                        "sellerSku"                     => $skuMap['seller_sku'],
                        "quantity"                      => $item->getQty(),
                        "sellerFulfillmentOrderItemId"  => $item->getItemId()
                    ];
                }
            }
            if (!empty($address["name"]) && !empty($address["addressLine1"]) && !empty($address["city"])
                && !empty($address["postalCode"]) && !empty($address["phone"]) && count($items) > 0) {
                $requstArr['address'] = $address;
                $requstArr['items'] = $items;
                $data = ['data' => $requstArr];
                $fullfillmentResponse = $this->sendfullfillmentPreviewRequest($data);
                $quoteTotal = $this->cart->getQuote()->getSubtotal();
                if (isset($fullfillmentResponse['payload']['fulfillmentPreviews'])) {
                    $i = 0;
                    $method = "";
                    $shipmentData = [];
                    foreach ($fullfillmentResponse['payload']['fulfillmentPreviews'] as $item) {
                        $method = $item['shippingSpeedCategory'];
                        if (isset($item['fulfillmentPreviewShipments']) &&
                           isset($item['estimatedFees'])
                        ) {
                            $estimatedFee = $item['estimatedFees'][2];
                            $latestArrivalDate ='';
                            if (isset($item['fulfillmentPreviewShipments'][0]['latestArrivalDate'])) {
                                $fulfillmentPreviewShipments = $item['fulfillmentPreviewShipments']['0'];
                                $arrivalDate = $fulfillmentPreviewShipments['latestArrivalDate'];
                                $latestArrivalDate = " Delivers ".date("m/d/Y", strtotime($arrivalDate));
                            }

                            $shipmentData[$i]['title'] = $method."".$latestArrivalDate;
                            $shipmentData[$i]['code'] = strtolower($method);
                            $shipmentData[$i]['rate'] = $estimatedFee['amount']['value'];

                            if ($this->isEnabledMethod($method) == '1') {
                                $shippingRates = $this->getShippingRate($method);
                                $rateOverRide = $this->getOrderOverride($method);
                                if ($shippingRates != '' && !empty($rateOverRide) && $quoteTotal >= $rateOverRide) {
                                    $shipmentData[$i]['rate'] = $shippingRates;
                                } else {
                                    $shipmentData[$i]['rate'] = $estimatedFee['amount']['value'];
                                }
                            }
                        }
                        $i++;
                    }
                    return $shipmentData;
                }
            }
            return false;
        } catch (\RuntimeException $e) {
            return false;
        } catch (\Exception $e) {
            return false;
        } catch (LocalizedException $e) {
            return false;
        }
    }
    /**
     * Send fullfillment Preview Request
     *
     * @param array $data
     * @return string
     */
    public function sendfullfillmentPreviewRequest($data)
    {
        $serializeData = $this->jsonEncoder->encode($data);
        try {
            $urlAppend ='FulfillmentPreview.php';
            $requestStatus ='';
            $params = ['item' => $serializeData];
            $token = $this->helper->getConfig('token');
            $headers = ["Content-Type" => "application/json",
            'Authorization' => $token];
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
                return false;
            }
        } catch (\Exception $e) {
            return $e;
        }
    }
    /**
     * RegionCode
     *
     * @param int $id
     * @return string
     */
    public function regionCode($id)
    {
        $region = $this->regionFactory->create()->load($id);
        return $region->getCode();
    }
    /**
     * Get RateSettingsValue
     *
     * @param string $config_path
     * @return bool
     */
    public function getRateSettingsValue($config_path)
    {
        $scopeId = $this->storeManager->getStore()->getWebsiteId();
        $data = $this->helper->getConfigPathVariables();
        $path = $data[$config_path];
        $configValue = $this->scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_WEBSITE,
            $scopeId
        );
        return $configValue;
    }
    /**
     * IsEnabledMethod
     *
     * @param string $method
     * @return bool
     */
    public function isEnabledMethod($method)
    {
        $path = strtolower($method)."_enable";
        return $this->getRateSettingsValue($path);
    }
    /**
     * GetShippingRate
     *
     * @param string $method
     * @return bool
     */
    public function getShippingRate($method)
    {
        $path = strtolower($method)."_rate";
        return $this->getRateSettingsValue($path);
    }
    /**
     * GetOrderOverride
     *
     * @param string $method
     * @return string
     */
    public function getOrderOverride($method)
    {
        $path = strtolower($method)."_order";
        return $this->getRateSettingsValue($path);
    }
    /**
     * Get quote object associated with cart. By default it is current customer session quote
     *
     * @return \Magento\Quote\Model\Quote
     */
    public function getQuoteItemsStatus()
    {
        $website = $this->storeManager->getStore()->getWebsiteId();
        $quoteItemStatus = [];
        $mcfStatus = [];
        $quoteItems =  $this->cart->getQuote()->getAllItems();
        foreach ($quoteItems as $item) {
            $productId = $item->getProductId();
            $quoteItemId = $item->getId();
            $skuSearchCriteria = $this->searchCriteriaBuilder
            ->addFilter('product_id', $productId, 'eq')
            ->addFilter('website', $website, 'eq')->create();
            $skuMappingData = $this->skuMappingRepository->getByProductId($productId);
            $skuMappingData = $this->skuMappingRepository->getList($skuSearchCriteria);
            if ($skuMappingData->getTotalCount() > 0) {
                foreach ($skuMappingData->getItems() as $data) {
                    if ($data->getStatus() && $data->getSyncStatus()) {
                        $quoteItemStatus['items'][] = [ "quote_item_id" => $quoteItemId,
                                                        "product_id" => $productId,
                                                        "mcf" => 1
                                                      ];
                        $mcfStatus[] = 1;
                    } else {
                        $quoteItemStatus['items'][] = [ "quote_item_id" => $quoteItemId,
                                                        "product_id" => $productId,
                                                        "mcf" => 0
                                                      ];
                        $mcfStatus[] = 0;
                    }
                }
            } else {
                $mcfStatus[] = 0;
                $quoteItemStatus['items'][] = [ "quote_item_id" => $quoteItemId,
                                                "product_id" => $productId,
                                                "mcf" => 0
                                              ];
            }
        }
        $mcf = array_unique($mcfStatus);
        if (count($mcf) > 1) {
            $amazonMcfStatus = 2;
        } else {
            $amazonMcfStatus = $mcf[0];
        }
        $quoteItemStatus['amazon-mcf-status'] = $amazonMcfStatus;
        return $quoteItemStatus;
    }
}
