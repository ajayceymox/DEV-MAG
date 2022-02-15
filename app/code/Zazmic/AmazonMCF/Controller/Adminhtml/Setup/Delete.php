<?php declare(strict_types=1);
/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */
namespace Zazmic\AmazonMCF\Controller\Adminhtml\Setup;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\RequestInterface as Request;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\App\Cache\TypeListInterface;
use Zazmic\AmazonMCF\Model\McfStoreManager;
use Zazmic\AmazonMCF\Api\SkuMappingRepositoryInterface;
use Zazmic\AmazonMCF\Api\Data\SkuMappingInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Zazmic\AmazonMCF\Model\ConfigManager;
use Zazmic\AmazonMCF\Model\McfLogManager;

class Delete extends Action
{
    /**
     * @var RequestInterface
     */
    private $request;
    /**
     * @var PageFactory
     */
    private $resultPageFactory;
    /**
     * @var RedirectFactory
     */
    private $redirectFactory;
    /**
     * @var TypeListInterface
     */
    private $cacheTypeList;
    /**
     * @var McfStoreManager
     */
    private $mcfStoreManager;
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
     * @var McfLogManager
     */
    private $mcfLogManager;

    /**
     * @param Context $context
     * @param Request $request
     * @param PageFactory $resultPageFactory
     * @param RedirectFactory $redirectFactory
     * @param TypeListInterface $cacheTypeList
     * @param McfStoreManager $mcfStoreManager
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SkuMappingRepositoryInterface $skuMappingRepository
     * @param ConfigManager $configManager
     * @param McfLogManager $mcfLogManager
     */
    public function __construct(
        Context $context,
        Request $request,
        PageFactory $resultPageFactory,
        RedirectFactory $redirectFactory,
        TypeListInterface $cacheTypeList,
        McfStoreManager $mcfStoreManager,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SkuMappingRepositoryInterface $skuMappingRepository,
        configManager $configManager,
        McfLogManager $mcfLogManager
    ) {
        $this->request = $request;
        $this->resultPageFactory = $resultPageFactory;
        $this->cacheTypeList = $cacheTypeList;
        $this->mcfStoreManager = $mcfStoreManager;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->skuMappingRepository = $skuMappingRepository;
        $this->configManager = $configManager;
        $this->mcfLogManager = $mcfLogManager;
        parent::__construct($context);
    }

    /**
     * Execute
     *
     * @return \Magento\Framework\Controller\Result\RedirectFactory
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Zazmic_AmazonMCF::setup');
        $resultPage->addBreadcrumb(__('setup'), __('setup'));
        $resultPage->getConfig()->getTitle()->prepend(__('Amazon Multi-Channel Fulfillment (MCF) Setup'));
        $data = ["app_id"            => null,
                 "country"           => null,
                 "version"           => null,
                 "spapi_oauth_code"  => null,
                 "state"             => null,
                 "selling_partner_id" => null,
                 "token"              => null,
                 "status"             => null,
                 'website' => $this->request->getParam('website')
                ];
        if ($data) {
            try {
                if ($this->mcfStoreManager->disConnectStore($data)) {
                    $website=$this->request->getParam('website');
                    $searchCriteria = $this->searchCriteriaBuilder
                    ->addFilter('website', $website, 'eq')
                    ->create();
                    $skuData = $this->skuMappingRepository->getList($searchCriteria);
                    if ($skuData->getTotalCount() > 0) {
                        foreach ($skuData->getItems() as $value) {
                            $skuMapId = $value['id'];
                            $productId = $value['product_id'];
                            $this->skuMappingRepository->deleteById($skuMapId);
                            $this->configManager->updateProduct($productId, 0, $website);
                        }
                    }
                    $logData = [
                        'area' => 'Delete Store',
                        'type' => 'Info',
                        'details' => 'Deleted the Store successfully',
                    ];
                    $this->messageManager->addSuccessMessage(__("Deleted the store successfully"));
                } else {
                    $logData = [
                        'area' => 'Delete Store',
                        'type' => 'Info',
                        'details' => 'Something went wrong while deleting store',
                    ];
                    $this->messageManager->addSuccessMessage(__("Something went wrong while saving the data"));
                }
            } catch (LocalizedException $e) {
                $logData = [
                    'area' => 'Delete Store',
                    'type' => 'Error',
                    'details' => $e->getMessage(),
                ];
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }
        if (isset($logData)) {
            $this->mcfLogManager->addLog($logData);
        }
        $this->cacheTypeList->cleanType("config");
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/index');
    }
}
