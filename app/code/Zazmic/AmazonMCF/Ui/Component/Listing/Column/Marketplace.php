<?php
declare(strict_types=1);

/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */

namespace Zazmic\AmazonMCF\Ui\Component\Listing\Column;
 
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Zazmic\AmazonMCF\Api\SkuMappingRepositoryInterface;
use Magento\Ui\Component\Listing\Columns\Column;
use Zazmic\AmazonMCF\Model\ConfigManager;
use Magento\Framework\App\RequestInterface as Request;
use Zazmic\AmazonMCF\Helper\Data;
use Magento\Store\Model\StoreManagerInterface;

class Marketplace extends Column
{
    /**
     * @var ContextInterface
     */
    protected $context;
    /**
     * @var UiComponentFactory
     */
    protected $uiComponentFactory;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var SkuMappingRepositoryInterface
     */
    private $skuMappingRepository;
    /**
     * @var ConfigManager
     */
    private $configManager;
    /**
     * @var Request
     */
    private $request;
    /**
     * @var Data
     */
    private $helper;
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SkuMappingRepositoryInterface $skuMappingRepository
     * @param ConfigManager $configManager
     * @param Request $request
     * @param Data $helper
     * @param StoreManagerInterface $storeManager
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SkuMappingRepositoryInterface $skuMappingRepository,
        ConfigManager $configManager,
        Request $request,
        Data $helper,
        StoreManagerInterface $storeManager,
        array $components = [],
        array $data = []
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->skuMappingRepository = $skuMappingRepository;
        $this->configManager = $configManager;
        $this->request = $request;
        $this->helper = $helper;
        $this->storeManager = $storeManager;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }
 
    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if ($this->request->getParam('website')) {
            $activeWebsites = $this->request->getParam('website');
        } else {
            $activeWebsites = $this->storeManager->getDefaultStoreView()->getWebsiteId();
        }
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                $searchCriteria = $this->searchCriteriaBuilder
                ->addFilter('product_id', $item['entity_id'], 'eq')
                ->addFilter('website', $activeWebsites, 'eq')
                ->create();
                $skuData = $this->skuMappingRepository->getList($searchCriteria);
                if (count($skuData->getItems()) > 0) {
                    foreach ($skuData->getItems() as $value) {

                        if (!empty($value['seller_sku'])) {
                            $item[$fieldName] = $this->helper->getCountryName($activeWebsites, 'country');
                        } else {
                            $item[$fieldName] = "";
                        }
                    }
                }
            }
        }
        return $dataSource;
    }
}
