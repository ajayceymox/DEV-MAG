<?php declare(strict_types=1);
/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */
namespace Zazmic\AmazonMCF\Controller\Adminhtml\Inventory;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\LayoutFactory;
use Zazmic\AmazonMCF\Model\ConfigManager;
use Psr\Log\LoggerInterface;
use Magento\Framework\View\Result\PageFactory;

class InventorySync extends \Zazmic\AmazonMCF\Controller\Adminhtml\Inventory
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
        $this->configManager = $configManager;
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
        $resultPage->setActiveMenu('Zazmic_AmazonMCF::shipment_status');
        $resultPage->getConfig()->getTitle()->prepend(__('Inventory Sync'));

        try {
            $mcfItems=$this->configManager->inventorySync($this->getRequest()->getParam('website'));
            if (isset($mcfItems['success'])) {
                $this->messageManager->addSuccessMessage($mcfItems['msg']);
            } else {
                $this->messageManager->addErrorMessage($mcfItems['msg']);
            }
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(__("Failed to map inventory. $i", $e->getMessage()));
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('amazonmcf/shipmentstatus/index');
    }
}

