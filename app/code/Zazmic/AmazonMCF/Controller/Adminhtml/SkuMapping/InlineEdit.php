<?php
declare(strict_types=1);
/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */

namespace Zazmic\AmazonMCF\Controller\Adminhtml\SkuMapping;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\LayoutFactory;
use Psr\Log\LoggerInterface;
use Magento\Backend\App\Action;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\RequestInterface as Request;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Zazmic\AmazonMCF\Api\SkuMappingRepositoryInterface;
use Zazmic\AmazonMCF\Api\Data\SkuMappingInterface;
use Zazmic\AmazonMCF\Api\Data\SkuMappingInterfaceFactory;
use Zazmic\AmazonMCF\Helper\Data;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Zazmic\AmazonMCF\Model\ConfigManager;
use Zazmic\AmazonMCF\Model\McfLogManager;
use Magento\Framework\Stdlib\DateTime\DateTime;

class InlineEdit extends \Zazmic\AmazonMCF\Controller\Adminhtml\SkuMapping
{
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
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;
    /**
     * @var Request
     */
    private $request;
    /**
     * @var PageFactory
     */
    private $resultPageFactory;
    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;
    /**
     * @var RedirectFactory
     */
    private $redirectFactory;
    /**
     * @var JsonFactory
     */
    private $jsonFactory;
    /**
     * @var SkuMappingRepositoryInterface
     */
    private $skuMappingRepository;
    /**
     * @var SkuMappingInterfaceFactory
     */
    private $skuMappingInterfaceFactory;
    /**
     * @var Data
     */
    private $helper;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var ConfigManager
     */
    private $configManager;
    /**
     * @var McfLogManager
     */
    private $mcfLogManager;
    /**
     * @var DateTime
     */
    private $date;
    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param LayoutFactory $resultLayoutFactory
     * @param LoggerInterface $logger
     * @param Request $request
     * @param PageFactory $resultPageFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param RedirectFactory $redirectFactory
     * @param JsonFactory $jsonFactory
     * @param SkuMappingRepositoryInterface $skuMappingRepository
     * @param SkuMappingInterfaceFactory $skuMappingInterfaceFactory
     * @param Data $helper
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param ConfigManager $configManager
     * @param McfLogManager $mcfLogManager
     * @param DateTime $date
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        LayoutFactory $resultLayoutFactory,
        LoggerInterface $logger,
        Request $request,
        PageFactory $resultPageFactory,
        DataObjectHelper $dataObjectHelper,
        RedirectFactory $redirectFactory,
        JsonFactory $jsonFactory,
        SkuMappingRepositoryInterface $skuMappingRepository,
        SkuMappingInterfaceFactory $skuMappingInterfaceFactory,
        Data $helper,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ConfigManager $configManager,
        McfLogManager $mcfLogManager,
        DateTime $date
    ) {
        $this->request = $request;
        $this->resultPageFactory = $resultPageFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->skuMappingInterfaceFactory = $skuMappingInterfaceFactory;
        $this->skuMappingRepository = $skuMappingRepository;
        $this->jsonFactory = $jsonFactory;
        $this->helper = $helper;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->config = $configManager;
        $this->mcfLogManager = $mcfLogManager;
        $this->date = $date;
        parent::__construct($context, $coreRegistry, $resultLayoutFactory, $logger, $configManager);
    }
    /**
     * Execute
     */
    public function execute()
    {
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];
        $date = $this->date->gmtDate();
        if ($this->getRequest()->getParam('isAjax')) {
            $postItems = $this->getRequest()->getParam('items', []);
            if (!count($postItems)) {
                    $messages[] = __('Please correct the data entered.');
                    $error = true;
            } else {
                try {
                    if ($this->request->getParam('website')) {
                        $activeWebsites[] = [$this->request->getParam('website')];
                    } else {
                        $activeWebsites = $this->helper->getActiveConnections();
                    }
                    foreach ($activeWebsites as $activeWebsite) {
                        if ($this->helper->isConnectedExist($activeWebsite[0], 'spapi_oauth_code')) {
                            $productId = '';
                            $sellerSku = '';
                            $tableId = '';
                            $website = '';
                            $skuId = '';
                            $mappedSku = '';
                            foreach ($postItems as $postItem) {
                                $productId = $postItem['entity_id'];
                                if (isset($postItem['seller_sku'])) {
                                    $sellerSku = $postItem['seller_sku'];
                                }
                            }
                            if ($sellerSku) {
                                $skuSearchCriteria = $this->searchCriteriaBuilder
                                ->addFilter('seller_sku', $sellerSku, 'eq')
                                ->addFilter('website', $activeWebsite[0], 'eq')->create();
                                $skuData = $this->skuMappingRepository->getList($skuSearchCriteria);
                                foreach ($skuData->getItems() as $skuValue) {
                                    $skuId = $skuValue['id'];
                                }
                            }
                            if ($skuId) {
                                $messages[] = __('The Seller Sku is Already mapped with another Product.');
                                $error = true;
                                $this->messageManager->addErrorMessage('The Seller Sku is Already
                                mapped with another Product.');
                            } else {
                                $searchCriteria = $this->searchCriteriaBuilder
                                ->addFilter('product_id', $productId, 'eq')
                                ->addFilter('website', $activeWebsite[0], 'eq')->create();
                                $tableData = $this->skuMappingRepository->getList($searchCriteria);
                                foreach ($tableData->getItems() as $value) {
                                    $tableId = $value['id'];
                                    $mappedSku = $value['sku'];
                                }
                                $response = $this->config->getMcfMapStatus('inventory', $sellerSku, $activeWebsite[0]);
                                if (isset($response['seller_sku'])) {
                                    $logData = [
                                        'area' => __('Inline Edit'),
                                        'type' => __('Info'),
                                        'details' => __("<b>%1</b> mapped successfully.",$mappedSku),
                                    ];    
                                    $seller_sku = $response['seller_sku'];
                                    $asin = $response['asin'];
                                    $amcfProductName = $response['productName'];
                                    $website = $activeWebsite[0];
                                    $status = 1;
                                    $syncStatus =1;
                                } else {
                                    $logData = [
                                        'area' => __('Inline Edit'),
                                        'type' => __('Info'),
                                        'details' => __("<b>%1</b> unmapped successfully.",$mappedSku),
                                    ];    
                                    $seller_sku = '';
                                    $asin = '';
                                    $amcfProductName = '';
                                    $status = '';
                                    $syncStatus = '';
                                    $website = $activeWebsite[0];
                                }
                                if ($tableId) {
                                    $rawData = $this->skuMappingRepository->getById($tableId);
                                    $rawData->setSellerSku($seller_sku);
                                    $rawData->setAsin($asin);
                                    $rawData->setAmazonProductName($amcfProductName);
                                    $rawData->setWebsite($website);
                                    $rawData->setStatus($status);
                                    $rawData->setSyncStatus($syncStatus);
                                    $rawData->setUpdatedAt($date);
                                    $result = $this->skuMappingRepository->save($rawData);
                                    $this->config->updateProduct($productId, $status, $activeWebsite[0]);
                                    $this->messageManager->addSuccessMessage(__("Saved successfully"));
                                    $messages[] = __('Saved successfully');
                                } else {
                                    $product = $this->helper->getProductById($productId);
                                    if ($product && in_array($activeWebsite[0], $product->getWebsiteIds())) {
                                        $data = [
                                        'product_id' => $productId,
                                        'sku' => $product->getSku(),
                                        'seller_sku' => $seller_sku,
                                        'asin' => $asin,
                                        'product_name' =>  $amcfProductName,
                                        'amazon_product_name' => $amcfProductName,
                                        'status' => $status,
                                        'sync_status' => $syncStatus,
                                        'website' => $activeWebsite[0]
                                        ];
                                        $collection = $this->skuMappingInterfaceFactory->create();
                                        $this->dataObjectHelper->populateWithArray(
                                            $collection,
                                            $data,
                                            skuMappingInterface::class
                                        );
                                        $result = $this->skuMappingRepository->save($collection);
                                        $this->config->updateProduct($productId, $status, $activeWebsite[0]);
                                        if(!empty($seller_sku)) {
                                            $type = "Info";
                                            $msg = __($seller_sku." mapped successfully.");
                                            $this->messageManager->addSuccessMessage(__("Saved successfully"));
                                        } else {
                                            $type = "Error";
                                            $msg = __("No response found with given seller SKU (%1) to map.",$postItem['seller_sku']);
                                            $this->messageManager->addErrorMessage(__("No response found with given seller SKU(%1) to map.", $postItem['seller_sku']));
                                        }
                                        $logData = [
                                            'area' => __('Inline Edit'),
                                            'type' => $type,
                                            'details' => $msg,
                                        ];    
                                    }
                                    $this->messageManager->addSuccessMessage(__("Saved successfully"));
                                }
                            }
                        } elseif ($this->request->getParam('website')) {
                            $logData = [
                                'area' => __('Inline Edit'),
                                'type' => __('Error'),
                                'details' => __('Amazon store is not connected to the selected website.'),
                            ];    
                            $messages[] = __('Amazon store is not connected to the selected website.');
                            $error = true;
                        }
                    }
                } catch (LocalizedException $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                    $logData = [
                        'area' => __('Inline Edit'),
                        'type' => __('Error'),
                        'details' => __($e->getMessage()),
                    ];    
                }
                if (isset($logData)) {
                    $this->mcfLogManager->addLog($logData);
                }
            }
        }
        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }
}
