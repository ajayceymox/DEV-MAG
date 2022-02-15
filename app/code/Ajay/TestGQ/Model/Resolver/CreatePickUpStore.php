<?php
declare(strict_types=1);

namespace Ajay\TestGQ\Model\Resolver;

use Ajay\TestGQ\Api\Data\StoreInterface;
use Ajay\TestGQ\Api\Data\StoreInterfaceFactory;
use Ajay\TestGQ\Api\StoreRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;

class CreatePickUpStore
{

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;
    /**
     * @var StoreRepositoryInterface
     */
    private $storeRepository;
    /**
     * @var StoreInterfaceFactory
     */
    private $storeFactory;

    /**
     * @param DataObjectHelper $dataObjectHelper
     * @param StoreRepositoryInterface $storeRepository
     * @param StoreInterfaceFactory $storeInterfaceFactory
     */
    public function __construct(
        DataObjectHelper $dataObjectHelper,
        StoreRepositoryInterface $storeRepository,
        StoreInterfaceFactory $storeInterfaceFactory
    ) {
        $this->dataObjectHelper = $dataObjectHelper;
        $this->storeRepository = $storeRepository;
        $this->storeFactory = $storeInterfaceFactory;
    }

    /**
     * @param array $data
     * @return StoreInterface
     * @throws GraphQlInputException
     */
    public function execute(array $data): StoreInterface
    {
        try {
            $this->vaildateData($data);
            $store = $this->saveStore($this->createStore($data));
        } catch (\Exception $e) {
            throw new GraphQlInputException(__($e->getMessage()));
        }

        return $store;
    }

    /**
     * Guard function to handle bad request.
     * @param array $data
     * @throws LocalizedException
     */
    private function vaildateData(array $data)
    {
        if (!isset($data[StoreInterface::NAME])) {
            throw new LocalizedException(__('Name must be set'));
        }
    }

    /**
     * Persists the given store in the data base.
     * +
     * @param StoreInterface $store
     * @return StoreInterface
     * @throws CouldNotSaveException
     */
    private function saveStore(StoreInterface $store): StoreInterface
    {
        $this->storeRepository->save($store);

        return $store;
    }

    /**
     * Create a store dto by given data array.
     *
     * @param array $data
     * @return StoreInterface
     * @throws CouldNotSaveException
     */
    private function createStore(array $data): StoreInterface
    {
        /** @var StoreInterface $store */
        $store = $this->storeFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $store,
            $data,
            StoreInterface::class
        );

        return $store;
    }
}