<?php declare(strict_types=1);

/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */

namespace Zazmic\AmazonMCF\Controller\Adminhtml\SkuMapping;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\LayoutFactory;
use Magento\Framework\View\Result\PageFactory;
use Psr\Log\LoggerInterface;
use Zazmic\AmazonMCF\Model\ConfigManager;

class CheckSku extends \Zazmic\AmazonMCF\Controller\Adminhtml\SkuMapping
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;
    /**
     * @var Registry
     */
    private $coreRegistry;
    /**
     * @var LayoutFactory
     */
    private $resultLayoutFactory;
    /**
     * @var PageFactory
     */
    private $resultPageFactory;
    /**
     * @var ConfigManager
     */
    private $configManager;
    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param LayoutFactory $resultLayoutFactory
     * @param LoggerInterface $logger
     * @param PageFactory $resultPageFactory
     * @param ConfigManager $configManager
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        LayoutFactory $resultLayoutFactory,
        LoggerInterface $logger,
        PageFactory $resultPageFactory,
        ConfigManager $configManager
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context, $coreRegistry, $resultLayoutFactory, $logger, $configManager);
    }
    /**
     * Sku Mapping index action
     *
     * @return \Magento\Backend\Model\View\Result\Forward
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Zazmic_AmazonMCF::sku_mapping');
        $resultPage->getConfig()->getTitle()->prepend(__('SKU Mapping'));

        return $resultPage;
    }
}
