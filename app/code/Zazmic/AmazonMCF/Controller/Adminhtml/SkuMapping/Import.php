<?php declare(strict_types = 1);
/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */

namespace Zazmic\AmazonMCF\Controller\Adminhtml\SkuMapping;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\File\Csv;
use Magento\Framework\Image\AdapterFactory;
use Magento\Framework\Registry;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\View\Result\LayoutFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\ScopeInterface;
use Psr\Log\LoggerInterface;
use Zazmic\AmazonMCF\Model\ConfigManager;
use Magento\Framework\Message\ManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Zazmic\AmazonMCF\Model\McfLogManager;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\UrlInterface;
use Magento\Framework\MessageQueue\PublisherInterface;
use Magento\Framework\Serialize\Serializer\Json;

class Import extends \Zazmic\AmazonMCF\Controller\Adminhtml\SkuMapping
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
     * @var ConfigManager
     */
    private $configManager;
    /**
     * @var PageFactory
     */
    private $resultPageFactory;
    /**
     * @var RequestInterface
     */
    private $request;
    /**
     * @var Filesystem
     */
    private $fileSystem;
    /**
     * @var UploaderFactory
     */
    private $uploaderFactory;
    /**
     * @var Csv
     */
    private $csvProcessor;
    /**
     * @var ManagerInterface
     */
    protected $messageManager;
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var File
     */
    protected $file;
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;
    /**
     * @var McfLogManager
     */
    private $mcfLogManager;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var PublisherInterface
     */
    protected $publisher;
    /**
     * @var Json
     */
    private $json;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param LayoutFactory $resultLayoutFactory
     * @param LoggerInterface $logger
     * @param ConfigManager $configManager
     * @param PageFactory $resultPageFactory
     * @param RequestInterface $request
     * @param Filesystem $fileSystem
     * @param UploaderFactory $uploaderFactory
     * @param Csv $csvProcessor
     * @param ManagerInterface $messageManager
     * @param StoreManagerInterface $storeManager
     * @param File $file
     * @param ProductRepositoryInterface $productRepository
     * @param McfLogManager $mcfLogManager
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param PublisherInterface $publisher
     * @param Json $json
     */ 
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        LayoutFactory $resultLayoutFactory,
        LoggerInterface $logger,
        ConfigManager $configManager,
        PageFactory $resultPageFactory,
        RequestInterface $request,
        Filesystem $fileSystem,
        UploaderFactory $uploaderFactory,
        Csv $csvProcessor,
        ManagerInterface $messageManager,
        StoreManagerInterface $storeManager,
        File $file,
        ProductRepositoryInterface $productRepository,
        McfLogManager $mcfLogManager,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        PublisherInterface $publisher,
        Json $json
    ) {
        parent::__construct($context, $coreRegistry, $resultLayoutFactory, $logger, $configManager);
        $this->configManager = $configManager;
        $this->resultPageFactory = $resultPageFactory;
        $this->request = $request;
        $this->fileSystem = $fileSystem;
        $this->uploaderFactory = $uploaderFactory;
        $this->csvProcessor = $csvProcessor;
        $this->messageManager = $messageManager;
        $this->storeManager = $storeManager;
        $this->file = $file;
        $this->productRepository = $productRepository;
        $this->mcfLogManager = $mcfLogManager;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->publisher = $publisher;
        $this->logger = $logger;
        $this->json = $json;
    }
    /**
     * Execute
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Zazmic_AmazonMCF::sku_mapping');
        $resultPage->addBreadcrumb(__('SKU Mapping'), __('Import'));
        $resultPage->getConfig()->getTitle()->prepend(__('SKU Mapping'));
        $isPost = $this->getRequest()->getPost();
        if (isset($isPost['importdata'])) {
            try {
                $csvs  = $isPost['importdata']['0'];
                if ((isset($csvs['name'])) && ($csvs['size'] > 0)) {
                    $destinationPath = $csvs['path'];
                    if ($destinationPath == '') {
                        $this->messageManager->addSuccessMessage(__('File cannot be saved to path: %1', $destinationPath));
                        $resultRedirect = $this->resultRedirectFactory->create();
                        return $resultRedirect->setPath('amazonmcf/skumapping/import');
                    } else {
                        $filePath = 'skumap/tmp/import/'. $csvs['name'];
                        $mediaDirectory = $this->fileSystem->getDirectoryRead(DirectoryList::MEDIA);
                        $destinationfilePath = $mediaDirectory->getAbsolutePath($filePath);
                        $csvData = $this->csvProcessor->getData($destinationfilePath);
                        $data = $this->json->serialize($csvData);

                        $headerArray = ["item-name", "seller-sku", "asin"];
                        $headers = $csvData[0];
                        $mediaUrl = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
                        $uploadedCsv = $mediaUrl.$filePath;
                        if (count(array_intersect($headerArray, $headers)) != count($headerArray)) {
                            $this->messageManager->addErrorMessage(__("Invalid file format"));
                            $resultRedirect = $this->resultRedirectFactory->create();
                            return $resultRedirect->setPath('amazonmcf/skumapping/import');
                        } else {
                            $this->logger->info('AmazonMCF: Import SKU Map');
                            $this->publisher->publish('amazonmcf.queue.mapping', $data);
                            $this->messageManager->addSuccessMessage(__('Import request is added to queue'));
                        }
                        $logData = [
                            'area' => __('Import SKU'),
                            'type' => __('Info'),
                            'details' => '<a href="'.$uploadedCsv.'" target="_blank" download>CSV</a> added to import queue',
                        ];
                        if (isset($logData)) {
                            $this->mcfLogManager->addLog($logData);
                        }
                        $resultRedirect = $this->resultRedirectFactory->create();
                        return $resultRedirect->setPath('amazonmcf/skumapping/import');
                    }
                } else {
                    $this->messageManager->addErrorMessage(__("Please choose a valid csv file."));
                }
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__($e->getMessage()));
            }
        }
        return $resultPage;
    }
}
