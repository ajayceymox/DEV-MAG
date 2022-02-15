<?php
declare(strict_types=1);

/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */

namespace Zazmic\AmazonMCF\Controller\Adminhtml;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\LayoutFactory;
use Psr\Log\LoggerInterface;
use Zazmic\AmazonMCF\Model\ConfigManager;

abstract class SkuMapping extends \Magento\Backend\App\Action
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;
    /**
     * @var Context
     */
    private $context;
    /**
     * @var Registry
     */
    private $coreRegistry;
    /**
     * @var LayoutFactory
     */
    private $resultLayoutFactory;
    /**
     * @var ConfigManager
     */
    private $configManager;
    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param LayoutFactory $resultLayoutFactory
     * @param LoggerInterface $logger
     * @param ConfigManager $configManager
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        LayoutFactory $resultLayoutFactory,
        LoggerInterface $logger,
        ConfigManager $configManager
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->_resultLayoutFactory = $resultLayoutFactory;
        $this->logger = $logger;
        $this->configManager = $configManager;
    }
    /**
     * Allowed Status
     *
     * @return int
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Zazmic_AmazonMCF::sku_mapping');
    }
    /**
     * Connected Status
     *
     * @return int
     */
    protected function isConnected()
    {
        return $this->configManager->isConnected();
    }
}
