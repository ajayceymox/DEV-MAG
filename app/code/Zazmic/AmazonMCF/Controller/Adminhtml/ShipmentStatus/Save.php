<?php declare(strict_types=1);
/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */
namespace Zazmic\AmazonMCF\Controller\Adminhtml\ShipmentStatus;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\LayoutFactory;
use Psr\Log\LoggerInterface;
use Magento\Backend\App\Action;
use Magento\Framework\App\RequestInterface as Request;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Store\Model\ScopeInterface;
use Zazmic\AmazonMCF\Model\ConfigManager;
use Magento\Framework\Serialize\Serializer\Json;

class Save extends \Zazmic\AmazonMCF\Controller\Adminhtml\ShipmentStatus
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;
    /**
     * @var PageFactory
     */
    private $resultPageFactory;
    /**
     * @var RedirectFactory
     */
    private $redirectFactory;
    /**
     * @var ConfigManager
     */
    public $configManager;
    /**
     * @var Json
     */
    private $json;
    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param LayoutFactory $resultLayoutFactory
     * @param LoggerInterface $logger
     * @param Request $request
     * @param PageFactory $resultPageFactory
     * @param RedirectFactory $redirectFactory
     * @param ConfigManager $configManager
     * @param Json $json
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        LayoutFactory $resultLayoutFactory,
        LoggerInterface $logger,
        Request $request,
        PageFactory $resultPageFactory,
        RedirectFactory $redirectFactory,
        ConfigManager $configManager,
        Json $json
    ) {
        $this->request              = $request;
        $this->resultPageFactory    = $resultPageFactory;
        $this->resultRedirectFactory= $redirectFactory;
        $this->json = $json;
        parent::__construct($context, $coreRegistry, $resultLayoutFactory, $logger, $configManager);
    }

    /**
     * Execute
     *
     * @return \Magento\Framework\Controller\Result\RedirectFactory
     */
    public function execute()
    {
         /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Zazmic_AmazonMCF::shipping_rate');
        $postData = $this->request->getPostValue();
        if (isset($postData)) {
            $data = [];
            foreach ($postData['shipment_status'] as $post) {
                $scopeId = $post['scope_id'];
                foreach ($post as $key => $value) {
                    $configPath = '';
                    if ($key != 'scope_id') {
                        if ($key == 'shipment_sync_status') {
                            $configPath = 'zazmic/shipmentstatus/shipment_sync_status';
                        } elseif ($key == 'shipment_sync_interval') {
                            $configPath = 'zazmic/shipmentstatus/shipment_sync_interval';
                        }

                        $this->configManager->configUpdate(
                            $configPath,
                            $value,
                            ScopeInterface::SCOPE_WEBSITES,
                            $scopeId
                        );
                    }
                }
            }
            foreach ($postData['inventory'] as $post) {
                $scopeId = $post['scope_id'];
                foreach ($post as $key => $value) {
                    $configPath = '';
                    if ($key != 'scope_id') {
                        if ($key == 'auto_inventory') {
                            $configPath = 'zazmic/inventory_config/auto_inventory';
                        } elseif ($key == 'sync_interval') {
                            $configPath = 'zazmic/inventory_config/sync_interval';

                        }
                        $this->configManager->configUpdate(
                            $configPath,
                            $value,
                            ScopeInterface::SCOPE_WEBSITES,
                            $scopeId
                        );
                    }
                }
            }
            $amzDefaultValues = [];

            foreach ($postData['carrier'] as $carrier) {
                $carrierScopeId = $carrier['scope_id'];
                foreach ($carrier as $keys => $values) {
                    $carrierConfigPath = '';
                    $defaultFields=["scope_id","default_method","default_amazon_method"];
                    if ($keys == 'default_method') {
                        $val = "";
                        foreach ($values as $k => $v) {
                            if ($v == '1') {
                                print $k;
                                $amzDefaultValues[$k] = $carrier['default_amazon_method'][$k];
                            }
                        }
                    }
                    if (!in_array($keys,$defaultFields)) {
                        $carrierConfigPath = 'carriers/amazonshipping/'.$keys;
                        $this->configManager->configUpdate(
                            $carrierConfigPath,
                            $values,
                            ScopeInterface::SCOPE_WEBSITES,
                            $carrierScopeId
                        );
                    }
                }
            }
            if ($amzDefaultValues != '') {
                $amzDefaultSpeed = $this->json->serialize($amzDefaultValues);
            }
            $carrierConfigPath = 'carriers/amazonshipping/default_method';
            $this->configManager->configUpdate(
                $carrierConfigPath,
                $amzDefaultSpeed,
                ScopeInterface::SCOPE_WEBSITES,
                $carrierScopeId
            );
            $this->messageManager->addSuccessMessage(__('Settings updated'));
        } else {
            $this->messageManager->addErrorMessage(__("Failed to save. Please try again"));
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/index', ['_current' => true]);
    }
}
