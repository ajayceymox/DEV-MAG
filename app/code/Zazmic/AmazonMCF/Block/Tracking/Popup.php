<?php declare(strict_types=1);
/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */
namespace Zazmic\AmazonMCF\Block\Tracking;

use Magento\Framework\Stdlib\DateTime\DateTimeFormatterInterface;
use Zazmic\AmazonMCF\Model\FulfillmentOrderManager;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;

/**
 * Tracking popup
 *
 * @api
 * @since 100.0.2
 */
class Popup extends \Magento\Shipping\Block\Tracking\Popup
{
    
    /**
     *
     * @var Context
     */
    protected $context;
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $registry;

    /**
     * @var DateTimeFormatterInterface
     */
    protected $dateTimeFormatter;

    /**
     * @var FulfillmentOrderManager
     */
    protected $fulfillmentOrderManager;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param DateTimeFormatterInterface $dateTimeFormatter
     * @param FulfillmentOrderManager $fulfillmentOrderManager
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        DateTimeFormatterInterface $dateTimeFormatter,
        FulfillmentOrderManager $fulfillmentOrderManager,
        array $data = []
    ) {
        $this->fulfillmentOrderManager = $fulfillmentOrderManager;
        parent::__construct($context, $registry, $dateTimeFormatter, $data);
    }

    /**
     * Retrieve array of tracking info
     *
     * @param  string $packageNumber
     * @return array
     */
    public function getAmazonTrackingPreviewInfo($packageNumber)
    {
        return $this->fulfillmentOrderManager->getPreviewTrackingDetails($packageNumber);
    }
}
