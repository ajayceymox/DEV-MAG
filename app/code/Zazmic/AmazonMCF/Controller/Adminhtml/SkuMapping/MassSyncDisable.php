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
use Magento\Ui\Component\MassAction\Filter;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Zazmic\AmazonMCF\Api\SkuMappingRepositoryInterface;
use Zazmic\AmazonMCF\Api\Data\SkuMappingInterface;
use Zazmic\AmazonMCF\Api\Data\SkuMappingInterfaceFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface as Request;
use Zazmic\AmazonMCF\Helper\Data;
use Zazmic\AmazonMCF\Model\McfLogManager;
use Magento\Framework\Stdlib\DateTime\DateTime;

class MassSyncDisable extends \Zazmic\AmazonMCF\Controller\Adminhtml\SkuMapping
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;
    /**
     * @var ConfigManager
     */
    private $configManager;
    /**
     * @var Filter
     */
    private $filter;
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;
    /**
     * @var SkuMappingRepositoryInterface
     */
    private $skuMappingRepository;
    /**
     * @var SkuMappingInterfaceFactory
     */
    private $skuMappingInterfaceFactory;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var Request
     */
    private $request;
    /**
     * @var Data
     */
    private $helper;
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
     * @param ConfigManager $configManager
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param SkuMappingRepositoryInterface $skuMappingRepository
     * @param SkuMappingInterfaceFactory $skuMappingInterfaceFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param Request $request
     * @param Data $helper
     * @param McfLogManager $mcfLogManager
     * @param DateTime $date
     */

    public function __construct(
        Context $context,
        Registry $coreRegistry,
        LayoutFactory $resultLayoutFactory,
        LoggerInterface $logger,
        ConfigManager $configManager,
        Filter $filter,
        CollectionFactory $collectionFactory,
        SkuMappingRepositoryInterface $skuMappingRepository,
        SkuMappingInterfaceFactory $skuMappingInterfaceFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Request $request,
        Data $helper,
        McfLogManager $mcfLogManager,
        DateTime $date
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->filter = $filter;
        $this->skuMappingRepository = $skuMappingRepository;
        $this->skuMappingInterfaceFactory = $skuMappingInterfaceFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->configManager = $configManager;
        $this->request = $request;
        $this->helper = $helper;
        $this->mcfLogManager = $mcfLogManager;
        $this->date = $date;
        parent::__construct($context, $coreRegistry, $resultLayoutFactory, $logger, $configManager);
    }
    /**
     * Sku Mapping index action
     *
     * @return \Magento\Backend\Model\View\Result\Forward
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $connected = $this->isConnected();
        $date = $this->date->gmtDate();
        if (empty($connected)) {
            return $resultRedirect->setPath('amazonmcf/setup/index/');
        }
        $websiteExist = 0 ;
        if ($this->request->getParam('website')) {
            $activeWebsites[] = [$this->request->getParam('website')];
            $websiteExist = $this->request->getParam('website');
        } else {
            $activeWebsites = $this->helper->getActiveConnections();
        }
        foreach ($activeWebsites as $activeWebsite) {
            if ($this->helper->isConnectedExist($activeWebsite[0], 'spapi_oauth_code')) {
                $collection = $this->filter->getCollection($this->collectionFactory->create());
                $collectionSize = $collection->getSize();
                $flagArr = [];
                foreach ($collection as $block) {
                    $searchCriteria = $this->searchCriteriaBuilder
                        ->addFilter('product_id', $block->getId(), 'eq')
                        ->addFilter('website', $activeWebsite[0], 'eq')->create();
                    $skuData = $this->skuMappingRepository->getList($searchCriteria);
                    if (count($skuData->getItems()) > 0) {
                        foreach ($skuData->getItems() as $value) {
                            $skuMapId = $value['id'];
                            $skuMapProductId = $value['product_id'];
                            $productSku = $value['sku'];
                        }
                        if ($block->getId() != $skuMapProductId) {
                            continue;
                        }
                        $rawData = $this->skuMappingRepository->getById($skuMapId);
                        if ($rawData->getStatus() == '1') {
                            $rawData->setSyncStatus('0');
                            $rawData->setUpdatedAt($date);
                            $result = $this->skuMappingRepository->save($rawData);
                        } else {
                            $flagArr[] = $block->getSku();
                        }
                    } else {
                        $flagArr[] = $block->getSku();
                    }
                }
            }
        }
        if (count($flagArr)>0) {
            $flag = implode(", ", $flagArr);
            $this->messageManager->addWarning(__('Product(s) with SKU <b>"%1"</b> not mapped yet to disable.', $flag));
            $logData = [
                'area' => __('Mass MCF Disable'),
                'type' => __('Error'),
                'details' => __('No mapped product(s) with SKU <b>"%1"</b> are found to disable.', $flag),
            ];    
            if (isset($logData)) {
                $this->mcfLogManager->addLog($logData);
            }
            $collectionSize = $collectionSize-count($flagArr);
        }
        $this->messageManager->addSuccess(__('A total of %1 record(s) have been disabled.', $collectionSize));
        $logData = [
            'area' => __('Mass MCF Disable'),
            'type' => __('Success'),
            'details' => __('A total of %1 record(s) have been disabled.', $collectionSize),
        ];    
        if (isset($logData)) {
            $this->mcfLogManager->addLog($logData);
        }
        if ($websiteExist  != 0) {
            return $resultRedirect->setPath('amazonmcf/skumapping/index/', ['website' => $websiteExist]);
        } else {
            return $resultRedirect->setPath('amazonmcf/skumapping/index/');
        }
    }
}
