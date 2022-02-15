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
use Zazmic\AmazonMCF\Api\SyncinfoRepositoryInterface;
use Zazmic\AmazonMCF\Api\Data\SyncinfoInterface;
use Zazmic\AmazonMCF\Api\Data\SyncinfoInterfaceFactory;
use Zazmic\AmazonMCF\Api\Data\SyncinfoSearchResultInterfaceFactory;
use Zazmic\AmazonMCF\Model\SyncinfoFactory;
use Zazmic\AmazonMCF\Model\ResourceModel\Syncinfo\CollectionFactory;
use Zazmic\AmazonMCF\Model\ResourceModel\Syncinfo;

class SyncinfoRepository implements SyncinfoRepositoryInterface
{
    /**
     * @var SyncinfoFactory
     */
    private $syncinfoFactory;
    /**
     * @var ResourceModel\Syncinfo
     */
    private $syncinfoResource;
    /**
     * @var SyncinfoInterfaceFactory
     */
    private $syncinfoDataFactory;
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
     * @var SyncinfoSearchResultInterfaceFactory
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
     * SyncinfoRepository constructor
     *
     * @param SyncinfoFactory $syncinfoFactory
     * @param Syncinfo $syncinfoResource
     * @param SyncinfoInterfaceFactory $syncinfoDataFactory
     * @param ExtensibleDataObjectConverter $dataObjectConverter
     * @param DataObjectHelper $dataObjectHelper
     * @param SyncinfoSearchResultInterfaceFactory $searchResultFactory
     * @param DataObjectProcessor $dataObjectProcessor
     * @param CollectionProcessorInterface $collectionProcessor
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        SyncinfoFactory $syncinfoFactory,
        Syncinfo $syncinfoResource,
        SyncinfoInterfaceFactory $syncinfoDataFactory,
        ExtensibleDataObjectConverter $dataObjectConverter,
        DataObjectHelper $dataObjectHelper,
        SyncinfoSearchResultInterfaceFactory $searchResultFactory,
        DataObjectProcessor $dataObjectProcessor,
        CollectionProcessorInterface $collectionProcessor,
        CollectionFactory $collectionFactory
    ) {
        $this->syncinfoFactory = $syncinfoFactory;
        $this->syncinfoResource = $syncinfoResource;
        $this->syncinfoDataFactory = $syncinfoDataFactory;
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
     * @return SyncinfoInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($id)
    {
        $syncinfoObj = $this->syncinfoFactory->create();
        $this->syncinfoResource->load($syncinfoObj, $id);
        if (!$syncinfoObj->getId()) {
            throw new NoSuchEntityException(__('Item with id "%1" does not exist.', $id));
        }
        $data = $this->syncinfoDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $data,
            $syncinfoObj->getData(),
            SyncinfoInterface::class
        );
        $data->setId($syncinfoObj->getId());
        return $data;
    }

    /**
     * Save Syncinfo Data
     *
     * @param SyncinfoInterface $syncinfo
     * @return SyncinfoInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(SyncinfoInterface $syncinfo)
    {
        $syncinfoData = $this->dataObjectConverter->toNestedArray(
            $syncinfo,
            [],
            SyncinfoInterface::class
        );
        $syncinfoModel = $this->syncinfoFactory->create();
        $syncinfoModel->setData($syncinfoData);
        try {
            $syncinfoModel->setId($syncinfo->getId());
            $this->syncinfoResource->save($syncinfoModel);
            if ($syncinfo->getId()) {
                $syncinfo = $this->getById($syncinfo->getId());
            } else {
                $syncinfo->setId($syncinfoModel->getId());
            }
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__('Could not save the data: %1', $exception->getMessage()));
        }
        return $syncinfo;
    }

    /**
     * Delete the Syncinfo by Category id
     *
     * @param int $id
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($id)
    {
        $syncinfoObj = $this->syncinfoFactory->create();
        $this->syncinfoResource->load($syncinfoObj, $id);
        $this->syncinfoResource->delete($syncinfoObj);
        if ($syncinfoObj->isDeleted()) {
            return true;
        }
        return false;
    }

    /**
     * Delete
     *
     * @param SyncinfoInterface $syncinfo
     * @return mixed
     * @throws CouldNotDeleteException
     */
    public function delete(SyncinfoInterface $syncinfo)
    {
        try {
            $this->resource->delete($syncinfo);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * Get List
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Zazmic\AmazonMCF\Api\Data\SyncinfoSearchResultsInterface
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
