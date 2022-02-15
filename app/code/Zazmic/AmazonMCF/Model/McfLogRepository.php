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
use Zazmic\AmazonMCF\Api\McfLogRepositoryInterface;
use Zazmic\AmazonMCF\Api\Data\McfLogInterface;
use Zazmic\AmazonMCF\Api\Data\McfLogInterfaceFactory;
use Zazmic\AmazonMCF\Api\Data\McfLogSearchResultInterfaceFactory;
use Zazmic\AmazonMCF\Model\McfLogFactory;
use Zazmic\AmazonMCF\Model\ResourceModel\McfLog\CollectionFactory;
use Zazmic\AmazonMCF\Model\ResourceModel\McfLog;

class McfLogRepository implements McfLogRepositoryInterface
{
    /**
     * @var McfLogFactory
     */
    private $mcfLogFactory;
    /**
     * @var ResourceModel\McfLog
     */
    private $mcfLogResource;
    /**
     * @var McfLogInterfaceFactory
     */
    private $mcfLogDataFactory;
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
     * @var McfLogSearchResultInterfaceFactory
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
     * McfLogRepository constructor
     *
     * @param McfLogFactory $mcfLogFactory
     * @param McfLog $mcfLogResource
     * @param McfLogInterfaceFactory $mcfLogDataFactory
     * @param ExtensibleDataObjectConverter $dataObjectConverter
     * @param DataObjectHelper $dataObjectHelper
     * @param McfLogSearchResultInterfaceFactory $searchResultFactory
     * @param DataObjectProcessor $dataObjectProcessor
     * @param CollectionProcessorInterface $collectionProcessor
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        McfLogFactory $mcfLogFactory,
        McfLog $mcfLogResource,
        McfLogInterfaceFactory $mcfLogDataFactory,
        ExtensibleDataObjectConverter $dataObjectConverter,
        DataObjectHelper $dataObjectHelper,
        McfLogSearchResultInterfaceFactory $searchResultFactory,
        DataObjectProcessor $dataObjectProcessor,
        CollectionProcessorInterface $collectionProcessor,
        CollectionFactory $collectionFactory
    ) {
        $this->mcfLogFactory = $mcfLogFactory;
        $this->mcfLogResource = $mcfLogResource;
        $this->mcfLogDataFactory = $mcfLogDataFactory;
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
     * @return McfLogInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($id)
    {
        $mcfLogObj = $this->mcfLogFactory->create();
        $this->mcfLogResource->load($mcfLogObj, $id);
        if (!$mcfLogObj->getId()) {
            throw new NoSuchEntityException(__('Item with id "%1" does not exist.', $id));
        }
        $data = $this->mcfLogDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $data,
            $mcfLogObj->getData(),
            McfLogInterface::class
        );
        $data->setId($mcfLogObj->getId());
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
        $mcfLog = $this->collectionFactory->create();
        $mcfLog->addFieldToFilter('product_id', $productId);
        $mcfLogData = $mcfLog->getData();
        if (!empty($mcfLogData)) {
            return $mcfLogData[0];
        }
    }

    /**
     * Save McfLog Data
     *
     * @param McfLogInterface $mcfLog
     * @return McfLogInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(McfLogInterface $mcfLog)
    {
        $mcfLogData = $this->dataObjectConverter->toNestedArray(
            $mcfLog,
            [],
            McfLogInterface::class
        );
        $mcfLogModel = $this->mcfLogFactory->create();
        $mcfLogModel->setData($mcfLogData);
        try {
            $mcfLogModel->setId($mcfLog->getId());
            $this->mcfLogResource->save($mcfLogModel);
            if ($mcfLog->getId()) {
                $mcfLog = $this->getById($mcfLog->getId());
            } else {
                $mcfLog->setId($mcfLogModel->getId());
            }
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__('Could not save the data: %1', $exception->getMessage()));
        }
        return $mcfLog;
    }

    /**
     * Delete the McfLog by Id
     *
     * @param int $id
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($id)
    {
        $mcfLogObj = $this->mcfLogFactory->create();
        $this->mcfLogResource->load($mcfLogObj, $id);
        $this->mcfLogResource->delete($mcfLogObj);
        if ($mcfLogObj->isDeleted()) {
            return true;
        }
        return false;
    }
    
    /**
     * Delete
     *
     * @param McfLogInterface $mcfLog
     * @return mixed
     * @throws CouldNotDeleteException
     */
    public function delete(McfLogInterface $mcfLog)
    {
        try {
            $this->resource->delete($mcfLog);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * Get List
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Zazmic\AmazonMCF\Api\Data\McfLogSearchResultsInterface
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
