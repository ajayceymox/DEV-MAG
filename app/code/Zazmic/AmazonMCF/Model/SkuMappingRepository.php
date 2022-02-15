<?php declare(strict_types=1);
/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */
namespace Zazmic\AmazonMCF\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use Zazmic\AmazonMCF\Api\SkuMappingRepositoryInterface;
use Zazmic\AmazonMCF\Api\Data\SkuMappingInterface;
use Zazmic\AmazonMCF\Api\Data\SkuMappingInterfaceFactory;
use Zazmic\AmazonMCF\Api\Data\SkuMappingSearchResultInterfaceFactory;
use Zazmic\AmazonMCF\Model\SkuMappingFactory;
use Zazmic\AmazonMCF\Model\ResourceModel\SkuMapping\CollectionFactory;
use Zazmic\AmazonMCF\Model\ResourceModel\SkuMapping;

class SkuMappingRepository implements SkuMappingRepositoryInterface
{
    /**
     * @var SkuMappingFactory
     */
    private $skuMappingFactory;
    /**
     * @var ResourceModel\SkuMapping
     */
    private $skuMappingResource;
    /**
     * @var SkuMappingInterfaceFactory
     */
    private $skuMappingDataFactory;
    /**
     * @var ExtensibleDataObjectConverter
     */
    private $dataObjectConverter;
    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;
    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;
    /**
     * @var SkuMappingSearchResultInterfaceFactory
     */
    private $searchResultFactory;
    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * SkuMappingRepository constructor
     *
     * @param SkuMappingFactory $skuMappingFactory
     * @param SkuMapping $skuMappingResource
     * @param SkuMappingInterfaceFactory $skuMappingDataFactory
     * @param ExtensibleDataObjectConverter $dataObjectConverter
     * @param DataObjectHelper $dataObjectHelper
     * @param SkuMappingSearchResultInterfaceFactory $searchResultFactory
     * @param DataObjectProcessor $dataObjectProcessor
     * @param CollectionProcessorInterface $collectionProcessor
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        SkuMappingFactory $skuMappingFactory,
        SkuMapping $skuMappingResource,
        SkuMappingInterfaceFactory $skuMappingDataFactory,
        ExtensibleDataObjectConverter $dataObjectConverter,
        DataObjectHelper $dataObjectHelper,
        SkuMappingSearchResultInterfaceFactory $searchResultFactory,
        DataObjectProcessor $dataObjectProcessor,
        CollectionProcessorInterface $collectionProcessor,
        CollectionFactory $collectionFactory
    ) {
        $this->skuMappingFactory = $skuMappingFactory;
        $this->skuMappingResource = $skuMappingResource;
        $this->skuMappingDataFactory = $skuMappingDataFactory;
        $this->dataObjectConverter = $dataObjectConverter;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->searchResultFactory = $searchResultFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->collectionProcessor = $collectionProcessor;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Get By Id
     *
     * @param int $id
     * @return SkuMappingInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($id)
    {
        $skuMappingObj = $this->skuMappingFactory->create();
        $this->skuMappingResource->load($skuMappingObj, $id);
        if (!$skuMappingObj->getId()) {
            throw new NoSuchEntityException(__('Item with id "%1" does not exist.', $id));
        }
        $data = $this->skuMappingDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $data,
            $skuMappingObj->getData(),
            SkuMappingInterface::class
        );
        $data->setId($skuMappingObj->getId());
        return $data;
    }
     /**
      * Get By Product Id
      *
      * @param int $productId
      * @return mixed
      * @throws NoSuchEntityException
      */
    public function getByProductId($productId)
    {
        $skuMapping = $this->collectionFactory->create();
        $skuMapping->addFieldToFilter('product_id', $productId);
        $skuMappingData = $skuMapping->getData();
        if (!empty($skuMappingData)) {
            return $skuMappingData[0];
        }
    }

    /**
     * Save SkuMapping Data
     *
     * @param SkuMappingInterface $skuMapping
     * @return SkuMappingInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(SkuMappingInterface $skuMapping)
    {
        $skuMappingData = $this->dataObjectConverter->toNestedArray(
            $skuMapping,
            [],
            SkuMappingInterface::class
        );
        $skuMappingModel = $this->skuMappingFactory->create();
        $skuMappingModel->setData($skuMappingData);
        try {
            $skuMappingModel->setId($skuMapping->getId());
            $this->skuMappingResource->save($skuMappingModel);
            if ($skuMapping->getId()) {
                $skuMapping = $this->getById($skuMapping->getId());
            } else {
                $skuMapping->setId($skuMappingModel->getId());
            }
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__('Could not save the data: %1', $exception->getMessage()));
        }
        return $skuMapping;
    }

    /**
     * Delete the SkuMapping by Id
     *
     * @param int $id
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($id)
    {
        $skuMappingObj = $this->skuMappingFactory->create();
        $this->skuMappingResource->load($skuMappingObj, $id);
        $this->skuMappingResource->delete($skuMappingObj);
        if ($skuMappingObj->isDeleted()) {
            return true;
        }
        return false;
    }
    
    /**
     * Delete
     *
     * @param SkuMappingInterface $skuMapping
     * @return mixed
     * @throws CouldNotDeleteException
     */
    public function delete(SkuMappingInterface $skuMapping)
    {
        try {
            $this->resource->delete($skuMapping);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * Get List
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Zazmic\AmazonMCF\Api\Data\SkuMappingSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->collectionFactory->create();

        $this->collectionProcessor->process($searchCriteria, $collection);

        $searchResults = $this->searchResultFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }
}
