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
use Zazmic\AmazonMCF\Helper\Data;
use Zazmic\AmazonMCF\Model\McfLogManager;

class Save extends Action
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
       * @var Data
       */
    private $helper;
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
     * @param Data $helper
     * @param McfLogManager $mcfLogManager
     */
    public function __construct(
        Context $context,
        Request $request,
        PageFactory $resultPageFactory,
        RedirectFactory $redirectFactory,
        TypeListInterface $cacheTypeList,
        McfStoreManager $mcfStoreManager,
        Data $helper,
        McfLogManager $mcfLogManager
    ) {
        $this->request = $request;
        $this->resultPageFactory = $resultPageFactory;
        $this->cacheTypeList = $cacheTypeList;
        $this->mcfStoreManager = $mcfStoreManager;
        $this->helper = $helper;
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
        $data = $this->request->getPostValue();
        if ($this->helper->isConnectedExist($data['website'], 'country')) {
            $this->messageManager->addErrorMessage(__("Already a connection exist with the selected website."));
        } else {
            if ($data['country']) {
                try {
                    if ($this->mcfStoreManager->addAmcfStore($data) == true) {
                        $logData = [
                            'area' => 'Add Amazon Account',
                            'type' => 'Info',
                            'details' => 'Added Amazon Account successfully',
                        ];
                        $this->messageManager->addSuccessMessage(__("Saved successfully"));
                    } else {
                        $logData = [
                            'area' => 'Add Amazon Account',
                            'type' => 'Error',
                            'details' => 'Something went wrong while adding Amazon Account',
                        ];
                        $this->messageManager->addErrorMessage(__("Something went wrong while saving the data"));
                    }
                    if (isset($logData)) {
                        $this->mcfLogManager->addLog($logData);
                    }
                } catch (LocalizedException $e) {
                    $logData = [
                        'area' => 'Disconnect Store',
                        'type' => 'Error',
                        'details' => $e->getMessage,
                    ];
                    if (isset($logData)) {
                        $this->mcfLogManager->addLog($logData);
                    }
                    $this->messageManager->addErrorMessage($e->getMessage());
                }
            } else {
                $this->messageManager->addErrorMessage(__("Please select required data"));
            }
        }
        $this->cacheTypeList->cleanType("config");
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/index');
    }
}
