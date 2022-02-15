<?php
namespace Zazmic\AmazonMCF\Model\Queue;

use Magento\Framework\Filesystem;
use Magento\Framework\File\Csv;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Zazmic\AmazonMCF\Model\ConfigManager;
use Magento\Framework\Message\ManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Zazmic\AmazonMCF\Model\McfLogManager;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\UrlInterface;
use Magento\Framework\MessageQueue\PublisherInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\App\Filesystem\DirectoryList;

class Consumer
{

    /**
     * @var ConfigManager
     */
    private $configManager;
   
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
     *
     * @param LoggerInterface $logger
     * @param ConfigManager $configManager
     * @param Filesystem $fileSystem
     * @param UploaderFactory $uploaderFactory
     * @param Csv $csvProcessor
     * @param ManagerInterface $messageManager
     * @param StoreManagerInterface $storeManager
     * @param ProductRepositoryInterface $productRepository
     * @param McfLogManager $mcfLogManager
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param PublisherInterface $publisher
     * @param Json $json
     */ 
    public function __construct(
        LoggerInterface $logger,
        ConfigManager $configManager,
        Filesystem $fileSystem,
        UploaderFactory $uploaderFactory,
        Csv $csvProcessor,
        ManagerInterface $messageManager,
        StoreManagerInterface $storeManager,
        ProductRepositoryInterface $productRepository,
        McfLogManager $mcfLogManager,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        PublisherInterface $publisher,
        Json $json
    ) {
        $this->configManager = $configManager;
        $this->fileSystem = $fileSystem;
        $this->uploaderFactory = $uploaderFactory;
        $this->csvProcessor = $csvProcessor;
        $this->messageManager = $messageManager;
        $this->storeManager = $storeManager;
        $this->productRepository = $productRepository;
        $this->mcfLogManager = $mcfLogManager;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->publisher = $publisher;
        $this->logger = $logger;
        $this->json = $json;
    }

    /**
    * @param string $data
    */
    public function process($data)
    {
        $this->logger->info('AmazonMCF: Import SKU Map running');
        $logData = [
            'user' => 'Cron Job',
            'area' => __('Import SKU'),
            'type' => __('Info'),
            'details' => 'Import sku map running',
        ];
        if (isset($logData)) {
            $this->mcfLogManager->addLog($logData);
        }
        $csvData = $this->json->unserialize($data);

        $reportFile = 'skumap/tmp/import/import-mcf-sku-'.date("Y-m-d-H-i:s").".csv";
        $mediaDirectory = $this->fileSystem->getDirectoryRead(DirectoryList::MEDIA);
        $reportFilePath = $mediaDirectory->getAbsolutePath($reportFile);
        $headerArray = ["item-name", "seller-sku", "asin"];
        $headers = $csvData[0];
        $csvHeaders = false;
        $mediaUrl = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
        $downloadCsv = $mediaUrl.$reportFile;
        if (count(array_intersect($headerArray, $headers)) != count($headerArray)) {
            $logData = [
                'user' => 'Cron Job',
                'area' => __('Import SKU'),
                'type' => __('Error'),
                'details' => 'Invalid file format',
            ];
            if (isset($logData)) {
                $this->mcfLogManager->addLog($logData);
            }
        } else {
            $reportData[0] = ["item-name", "seller-sku", "asin","error"];
            $count = 0;
            $i = 1;
            $flag = 0;
            foreach ($csvData as $importItem) {
                if (!$csvHeaders) {
                    $csvHeaders = $importItem;
                    continue;
                }
                $mapArr['asin'] = $importItem[array_search('asin', $headers)];
                $mapArr['seller_sku'] = $importItem[array_search('seller-sku', $headers)];
                $mapArr['product_name'] = $importItem[array_search('item-name', $headers)];
                $reportData[$i] = $mapArr;
                if (!empty($mapArr['seller_sku'])) {
                    $searchCriteria = $this->searchCriteriaBuilder
                    ->addFilter('sku', $mapArr['seller_sku'], 'eq')
                    ->create();
                    $productsItems  = $this->productRepository->getList($searchCriteria)->getItems();
                    if (count($productsItems) > 0) {
                        $mapResult = $this->configManager->skuMap($mapArr);
                        if (isset($mapResult['status'])) {
                            $count++;
                        }
                    } else{
                        $flag = 1;
                        $reportData[$i]['error'] = "No products found with the given seller-sku";                                   
                    }
                } else {
                    $flag = 1;
                    $reportData[$i]['error'] = "Empty seller-sku column";                                   
                }
                $i++;
            }
            if ($flag == 1) {
                    $this->csvProcessor->setEnclosure('"')
                            ->setDelimiter(',')
                            ->appendData($reportFilePath, $reportData,"w");
                    $this->messageManager->addComplexErrorMessage('import-skumap',[
                            'url' => $downloadCsv
                        ]);
            }                            
            $logData = [
                'user' => 'Cron Job',
                'area' => __('Import SKU'),
                'type' => __('Info'),
                'details' => 'Import SKU Completed, See the report <a href="'.$downloadCsv.'" target="_blank" download>here</a>',
            ];
            if (isset($logData)) {
                $this->mcfLogManager->addLog($logData);
            }
            $this->logger->info('AmazonMCF: Import SKU Map Completed');
        }
    }
}
