<?php
declare(strict_types=1);

/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */

namespace Zazmic\AmazonMCF\Ui\Component\Listing\Column;
 
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Zazmic\AmazonMCF\Api\SkuMappingRepositoryInterface;
use Zazmic\AmazonMCF\Model\SkuMappingFactory;

class Asin extends Column
{
    /**
     *
     * @param ContextInterface              $context
     * @param UiComponentFactory            $uiComponentFactory
     * @param SearchCriteriaBuilder         $searchCriteriaBuilder
     * @param SkuMappingRepositoryInterface $skuMappingRepositoryInterface
     * @param SkuMappingFactory             $skuMappingFactory
     * @param array                         $components
     * @param array                         $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SkuMappingRepositoryInterface $skuMappingRepositoryInterface,
        SkuMappingFactory $skuMappingFactory,
        array $components = [],
        array $data = []
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->skuMappingRepositoryInterface = $skuMappingRepositoryInterface;
        $this->skuMappingFactory = $skuMappingFactory;
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
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                $skuMapping = $this->skuMappingFactory->create()->load($item['entity_id']);
                foreach ($skuMapping as $data) {
                    if (isset($data['asin'])) {
                        $item[$fieldName] = $data['asin'];
                    } else {
                        $item[$fieldName] = "";
                    }
                }
            }
        }
        return $dataSource;
    }
}
