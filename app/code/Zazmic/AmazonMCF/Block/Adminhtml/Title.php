<?php declare(strict_types=1);
/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */
namespace Zazmic\AmazonMCF\Block\Adminhtml;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Zazmic\AmazonMCF\Helper\Data;

class Title extends \Magento\Theme\Block\Html\Title
{
    const XML_PATH_HEADER_TRANSLATE_TITLE = 'design/header/translate_title';
    /**
     * @var Context
     */
    private $context;
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;
    /**
     * @var Data
     */
    private $helper;
    /**
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param Data $helper
     * @param array $data
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        Data $helper,
        array $data = []
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->helper = $helper;
        parent::__construct($context, $scopeConfig, $data);
    }

    /**
     * Check active connections
     *
     * @return  array
     */
    public function getActiveConnections()
    {
        return $this->helper->getActiveConnections();
    }
}
