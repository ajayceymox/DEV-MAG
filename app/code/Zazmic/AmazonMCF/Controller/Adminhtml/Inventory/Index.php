<?php declare(strict_types=1);

/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */

namespace Zazmic\AmazonMCF\Controller\Adminhtml\Inventory;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\LayoutFactory;
use Magento\Framework\View\Result\PageFactory;
use Psr\Log\LoggerInterface;
use Zazmic\AmazonMCF\Model\ConfigManager;
use Magento\Framework\App\Cache\TypeListInterface;

class Index extends \Zazmic\AmazonMCF\Controller\Adminhtml\Inventory
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
     * @var TypeListInterface
     */
    private $cacheTypeList;
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
     * @param TypeListInterface $cacheTypeList
     * @param PageFactory $resultPageFactory
     * @param ConfigManager $configManager
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        LayoutFactory $resultLayoutFactory,
        LoggerInterface $logger,
        TypeListInterface $cacheTypeList,
        PageFactory $resultPageFactory,
        ConfigManager $configManager
    ) {
        $this->cacheTypeList = $cacheTypeList;
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
        $this->cacheTypeList->cleanType("config");
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Zazmic_AmazonMCF::inventory_sync');
        $resultPage->getConfig()->getTitle()->prepend(__('Inventory'));

        $params = $this->getRequest()->getParams();
        $params['scope'] = ScopeInterface::SCOPE_WEBSITES;
        $this->getRequest()->setParams($params);
        $connected = $this->isConnected();
        if (empty($connected)) {
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('amazonmcf/setup/index/');
        }
        return $resultPage;
    }
}
