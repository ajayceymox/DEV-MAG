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
use Zazmic\AmazonMCF\Api\InventorySyncinfoRepositoryInterface;
use Zazmic\AmazonMCF\Api\Data\InventorySyncinfoInterface;
use Zazmic\AmazonMCF\Api\Data\InventorySyncinfoInterfaceFactory;
use Zazmic\AmazonMCF\Api\Data\InventorySyncinfoSearchResultInterfaceFactory;
use Zazmic\AmazonMCF\Model\InventorySyncinfoFactory;
use Zazmic\AmazonMCF\Model\ResourceModel\InventorySyncinfo\CollectionFactory;
use Zazmic\AmazonMCF\Model\ResourceModel\InventorySyncinfo;

class InventorySyncinfoRepository implements InventorySyncinfoRepositoryInterface
{
    /**
     * @var InventorySyncinfoFactory
     */
    private $inventorySyncinfoFactory;
    /**
     * @var ResourceModel\InventorySyncinfo
     */
    private $inventorySyncinfoResource;
    /**
     * @var InventorySyncinfoInterfaceFactory
     */
    private $inventorySyncinfoDataFactory;
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
     * @var InventorySyncinfoSearchResultInterfaceFactory
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
     * InventorySyncinfoRepository constructor
     *
     * @param InventorySyncinfoFactory $inventorySyncinfoFactory
     * @param InventorySyncinfo $inventorySyncinfoResource
     * @param InventorySyncinfoInterfaceFactory $inventorySyncinfoDataFactory
     * @param ExtensibleDataObjectConverter $dataObjectConverter
     * @param DataObjectHelper $dataObjectHelper
     * @param InventorySyncinfoSearchResultInterfaceFactory $searchResultFactory
     * @param DataObjectProcessor $dataObjectProcessor
     * @param CollectionProcessorInterface $collectionProcessor
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        InventorySyncinfoFactory $inventorySyncinfoFactory,
        InventorySyncinfo $inventorySyncinfoResource,
        InventorySyncinfoInterfaceFactory $inventorySyncinfoDataFactory,
        ExtensibleDataObjectConverter $dataObjectConverter,
        DataObjectHelper $dataObjectHelper,
        InventorySyncinfoSearchResultInterfaceFactory $searchResultFactory,
        DataObjectProcessor $dataObjectProcessor,
        CollectionProcessorInterface $collectionProcessor,
        CollectionFactory $collectionFactory
    ) {
        $this->inventorySyncinfoFactory = $inventorySyncinfoFactory;
        $this->inventorySyncinfoResource = $inventorySyncinfoResource;
        $this->inventorySyncinfoDataFactory = $inventorySyncinfoDataFactory;
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
     * @return InventorySyncinfoInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($id)
    {
        $inventorySyncinfoObj = $this->inventorySyncinfoFactory->create();
        $this->inventorySyncinfoResource->load($inventorySyncinfoObj, $id);
        if (!$inventorySyncinfoObj->getId()) {
            throw new NoSuchEntityException(__('Item with id "%1" does not exist.', $id));
        }
        $data = $this->inventorySyncinfoDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $data,
            $inventorySyncinfoObj->getData(),
            InventorySyncinfoInterface::class
        );
        $data->setId($inventorySyncinfoObj->getId());
        return $data;
    }

    /**
     * Save InventorySyncinfo Data
     *
     * @param InventorySyncinfoInterface $inventorySyncinfo
     * @return InventorySyncinfoInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(InventorySyncinfoInterface $inventorySyncinfo)
    {
        $inventorySyncinfoData = $this->dataObjectConverter->toNestedArray(
            $inventorySyncinfo,
            [],
            InventorySyncinfoInterface::class
        );
        $inventorySyncinfoModel = $this->inventorySyncinfoFactory->create();
        $inventorySyncinfoModel->setData($inventorySyncinfoData);
        try {
            $inventorySyncinfoModel->setId($inventorySyncinfo->getId());
            $this->inventorySyncinfoResource->save($inventorySyncinfoModel);
            if ($inventorySyncinfo->getId()) {
                $inventorySyncinfo = $this->getById($inventorySyncinfo->getId());
            } else {
                $inventorySyncinfo->setId($inventorySyncinfoModel->getId());
            }
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__('Could not save the data: %1', $exception->getMessage()));
        }
        return $inventorySyncinfo;
    }

    /**
     * Delete the InventorySyncinfo by Category id
     *
     * @param int $id
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($id)
    {
        $inventorySyncinfoObj = $this->inventorySyncinfoFactory->create();
        $this->inventorySyncinfoResource->load($inventorySyncinfoObj, $id);
        $this->inventorySyncinfoResource->delete($inventorySyncinfoObj);
        if ($inventorySyncinfoObj->isDeleted()) {
            return true;
        }
        return false;
    }

    /**
     * Delete
     *
     * @param InventorySyncinfoInterface $inventorySyncinfo
     * @return mixed
     * @throws CouldNotDeleteException
     */
    public function delete(InventorySyncinfoInterface $inventorySyncinfo)
    {
        try {
            $this->resource->delete($inventorySyncinfo);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * Get List
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Zazmic\AmazonMCF\Api\Data\InventorySyncinfoSearchResultsInterface
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
