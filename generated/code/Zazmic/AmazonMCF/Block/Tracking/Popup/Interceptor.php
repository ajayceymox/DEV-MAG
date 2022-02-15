<?php
namespace Zazmic\AmazonMCF\Block\Tracking\Popup;

/**
 * Interceptor class for @see \Zazmic\AmazonMCF\Block\Tracking\Popup
 */
class Interceptor extends \Zazmic\AmazonMCF\Block\Tracking\Popup implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\View\Element\Template\Context $context, \Magento\Framework\Registry $registry, \Magento\Framework\Stdlib\DateTime\DateTimeFormatterInterface $dateTimeFormatter, \Zazmic\AmazonMCF\Model\FulfillmentOrderManager $fulfillmentOrderManager, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $registry, $dateTimeFormatter, $fulfillmentOrderManager, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function formatDeliveryDateTime($date, $time)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'formatDeliveryDateTime');
        if (!$pluginInfo) {
            return parent::formatDeliveryDateTime($date, $time);
        } else {
            return $this->___callPlugins('formatDeliveryDateTime', func_get_args(), $pluginInfo);
        }
    }
}
