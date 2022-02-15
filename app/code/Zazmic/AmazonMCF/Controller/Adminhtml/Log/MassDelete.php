<?php declare(strict_types=1);
/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */
namespace Zazmic\AmazonMCF\Controller\Adminhtml\Log;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\LayoutFactory;
use Psr\Log\LoggerInterface;
use Zazmic\AmazonMCF\Model\ConfigManager;
use Magento\Ui\Component\MassAction\Filter;
use Zazmic\AmazonMCF\Model\ResourceModel\McfLog\CollectionFactory;
use Zazmic\AmazonMCF\Api\McfLogRepositoryInterface;
use Zazmic\AmazonMCF\Api\Data\McfLogInterfaceFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;

class MassDelete extends \Zazmic\AmazonMCF\Controller\Adminhtml\Log
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;
    /**
     * @var ConfigManager
     */
    private $configManager;
    /**
     * @var Filter
     */
    private $filter;
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;
    /**
     * @var McfLogRepositoryInterface
     */
    private $mcfLogRepository;
    /**
     * @var McfLogInterfaceFactory
     */
    private $mcfLogInterfaceFactory;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param LayoutFactory $resultLayoutFactory
     * @param LoggerInterface $logger
     * @param ConfigManager $configManager
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param McfLogRepositoryInterface $mcfLogRepository
     * @param McfLogInterfaceFactory $mcfLogInterfaceFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        LayoutFactory $resultLayoutFactory,
        LoggerInterface $logger,
        ConfigManager $configManager,
        Filter $filter,
        CollectionFactory $collectionFactory,
        McfLogRepositoryInterface $mcfLogRepository,
        McfLogInterfaceFactory $mcfLogInterfaceFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->filter = $filter;
        $this->mcfLogRepository = $mcfLogRepository;
        $this->mcfLogInterfaceFactory = $mcfLogInterfaceFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->configManager = $configManager;
        parent::__construct($context, $coreRegistry, $resultLayoutFactory, $logger, $configManager);
    }
    /**
     * Sku Mapping index action
     *
     * @return \Magento\Backend\Model\View\Result\Forward
     */
    public function execute()
    {
        $connected = $this->isConnected();
        if (empty($connected)) {
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('amazonmcf/setup/index/');
        }
        $collection = $this->filter->getCollection($this->collectionFactory->create());

        $collectionSize  = $collection->getSize();
        $i=0;
        foreach ($collection as $block) {
            $rawData = $this->mcfLogRepository->deleteById($block->getId());
            $i++;
        }
        $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deleted.', $i));
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('amazonmcf/log/index/');
    }
}
