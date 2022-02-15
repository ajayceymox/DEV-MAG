<?php declare(strict_types=1);
/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */
namespace Zazmic\AmazonMCF\Plugin;

use Magento\Framework\UrlInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
 
class SalesOrderButton
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;
    
    /**
     * @param UrlInterface $urlBuilder
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        UrlInterface $urlBuilder,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->orderRepository = $orderRepository;
    }
    /**
     * Before SetLayout
     *
     * @param \Magento\Sales\Block\Adminhtml\Order\View $subject
     * @return null
     */
    public function beforeSetLayout(\Magento\Sales\Block\Adminhtml\Order\View $subject)
    {
        $orderId = $subject->getOrderId();
        $order = $this->orderRepository->get($orderId);
        $sendOrder =  $this->urlBuilder->getUrl('amazonmcf/ordersync', ['_current' => true,'order_id' => $orderId]);
        $status = $order->getFulfilledByAmazon();
        if ($status == 1 || $status == 2) {
            $subject->addButton(
                'syncorders',
                [
                'label' => __('Sync Order'),
                'onclick' => "setLocation('" . $sendOrder. "')",
                'class' => 'ship primary'
                ]
            );
        }
        return null;
    }
}
