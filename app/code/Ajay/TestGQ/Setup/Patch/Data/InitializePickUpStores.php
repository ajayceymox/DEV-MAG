<?php

declare(strict_types=1);

namespace Ajay\TestGQ\Setup\Patch\Data;

use Ajay\TestGQ\Api\Data\StoreInterface;
use Ajay\TestGQ\Api\Data\StoreInterfaceFactory;
use Ajay\TestGQ\Api\StoreRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class InitializePickUpStores implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;
    /**
     * @var StoreInterfaceFactory
     */
    private $storeInterfaceFactory;
    /**
     * @var StoreRepositoryInterface
     */
    private $storeRepository;
    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * EnableSegmentation constructor.
     *
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        StoreInterfaceFactory $storeInterfaceFactory,
        StoreRepositoryInterface $storeRepository,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->storeInterfaceFactory = $storeInterfaceFactory;
        $this->storeRepository = $storeRepository;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     * @throws Exception
     * @throws Exception
     */
    public function apply()
    {
        $this->moduleDataSetup->startSetup();
        $maxStore = 50;

        $citys = ['aaa', 'bbb', 'ccc', 'ddd', 'eee'];

        for ($i = 1; $i <= $maxStore; $i++) {

            $storeData = [
                StoreInterface::NAME => 'test Name ' . $i,
                StoreInterface::STREET => 'Test Street' . $i,
                StoreInterface::STREET_NUM => $i * random_int(1, 100),
                StoreInterface::CITY => $citys[random_int(0, 4)],
                StoreInterface::POSTCODE => $i * random_int(1000, 9999),
                StoreInterface::LATITUDE => random_int(4757549, 5041053) / 100000,
                StoreInterface::LONGITUDE => random_int(1157549, 1341053) / 100000,
            ];
            /** @var StoreInterface $store */
            $store = $this->storeInterfaceFactory->create();
            $this->dataObjectHelper->populateWithArray($store, $storeData, StoreInterface::class);
            $this->storeRepository->save($store);
        }

        $this->moduleDataSetup->endSetup();
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}