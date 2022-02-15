<?php declare(strict_types=1);
/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */
namespace Zazmic\AmazonMCF\Model\Carrier;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Psr\Log\LoggerInterface;
use Magento\Shipping\Model\Rate\ResultFactory;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;

use Zazmic\AmazonMCF\Model\FullfillmentManager;

/**
 * Amazon shipping model
 */
class Amazonshipping extends AbstractCarrier implements CarrierInterface
{
    /**
     * @var string
     */
    protected $_code = 'amazonshipping';
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;
    /**
     * @var ErrorFactory
     */
    private $rateErrorFactory;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var ResultFactory
     */
    private $rateResultFactory;
    /**
     * @var MethodFactory
     */
    private $rateMethodFactory;
    /**
     * @var FullfillmentManager
     */
    private $fullfillmentManager;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param ErrorFactory $rateErrorFactory
     * @param LoggerInterface $logger
     * @param ResultFactory $rateResultFactory
     * @param MethodFactory $rateMethodFactory
     * @param FullfillmentManager $fullfillmentManager
     * @param array $data
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ErrorFactory $rateErrorFactory,
        LoggerInterface $logger,
        ResultFactory $rateResultFactory,
        MethodFactory $rateMethodFactory,
        FullfillmentManager $fullfillmentManager,
        array $data = []
    ) {
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);

        $this->rateResultFactory = $rateResultFactory;
        $this->rateMethodFactory = $rateMethodFactory;
        $this->fullfillmentManager = $fullfillmentManager;
    }

    /**
     * Amazon Shipping Rates Collector
     *
     * @param RateRequest $request
     * @return \Magento\Shipping\Model\Rate\Result|bool
     */
    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }
        $mcfStatus = $this->fullfillmentManager->getQuoteItemsStatus();
        if ($mcfStatus['amazon-mcf-status']!=1) {
            return false;
        }
        $rateData = $this->fullfillmentManager->getfullfillmentShipmentRates();
        if (isset($rateData) && $rateData!='') {
            /** @var \Magento\Shipping\Model\Rate\Result $result */
            $result = $this->rateResultFactory->create();
            foreach ($rateData as $rate) {
                /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
                $method = $this->rateMethodFactory->create();
                $method->setCarrier($this->getCarrierCode());
                $method->setCarrierTitle($this->getConfigData('title'));
                $method->setMethod(strtolower($rate['code']));
                $method->setMethodTitle($rate['title']);
                $amount = $rate['rate'];
                $method->setPrice($amount);
                $method->setCost($amount);
                $result->append($method);
            }
            return $result;
        }
        return false;
    }

    /**
     * Get AllowedMethods
     *
     * @return array
     */
    public function getAllowedMethods()
    {
        return [$this->_code => $this->getConfigData('name')];
    }
}
