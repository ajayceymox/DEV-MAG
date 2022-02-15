<?php
declare(strict_types=1);

/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */

namespace Zazmic\AmazonMCF\Controller\Adminhtml\OrderSync;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\LayoutFactory;
use Psr\Log\LoggerInterface;
use Zazmic\AmazonMCF\Model\ConfigManager;

class Index extends \Magento\Backend\App\Action
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
        $this->coreRegistry = $coreRegistry;
        $this->resultLayoutFactory = $resultLayoutFactory;
        $this->logger = $logger;
        $this->configManager = $configManager;
    }

    /**
     * Execute
     */
    public function execute()
    {
        try {
                $orderId = $this->getRequest()->getParam('order_id');
                $resultRedirect = $this->resultRedirectFactory->create();
            if ($orderId) {
                $data = $this->configManager->manualOrderSync($orderId);
                $this->messageManager->addSuccessMessage("Order synced successfully");
                return $resultRedirect->setPath('sales/order/view/', ['order_id' => $orderId,'_current' => true]);
            } else {
                $data = $this->configManager->autoOrderStatusSync();
                $this->messageManager->addSuccessMessage("Order synced successfully");
                return $resultRedirect->setPath('sales/order/', ['_current' => true]);
            }
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(__("Failed to sync order", $e->getMessage()));
        }
    }
}
