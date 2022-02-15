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
use Zazmic\AmazonMCF\Model\McfLogManager;

class Disconnect extends Action
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
     * @var DataObjectHelper
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
     * @param McfLogManager $mcfLogManager
     */
    public function __construct(
        Context $context,
        Request $request,
        PageFactory $resultPageFactory,
        RedirectFactory $redirectFactory,
        TypeListInterface $cacheTypeList,
        McfStoreManager $mcfStoreManager,
        McfLogManager $mcfLogManager
    ) {
        $this->request = $request;
        $this->resultPageFactory = $resultPageFactory;
        $this->cacheTypeList = $cacheTypeList;
        $this->mcfStoreManager = $mcfStoreManager;
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
        $data = ['spapi_oauth_code' => null,
                 'selling_partner_id' => null,
                 "token"              => null,
                 'state' => null,
                 'website' => $this->request->getParam('website')
                ];
        if ($data) {
            try {
                if ($this->mcfStoreManager->disConnectStore($data)) {
                    $logData = [
                        'area' => 'Disconnect Store',
                        'type' => 'Info',
                        'details' => 'Disconnected the Store successfully',
                    ];
                    $this->messageManager->addSuccessMessage(__("Disconnected successfully"));
                } else {
                    $logData = [
                        'area' => 'Disconnect Store',
                        'type' => 'Error',
                        'details' => 'Something went wrong while disconnecting the store',
                    ];
                    $this->messageManager->addErrorMessage(__("Something went wrong while disconnecting the store"));
                }
                if (isset($logData)) {
                    $this->mcfLogManager->addLog($logData);
                }
            } catch (LocalizedException $e) {
                $logData = [
                    'area' => 'Disconnect Store',
                    'type' => 'Error',
                    'details' => 'Something went wrong while disconnecting the store',
                ];
                if (isset($logData)) {
                    $this->mcfLogManager->addLog($logData);
                }
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }
        $this->cacheTypeList->cleanType("config");
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/index');
    }
}
