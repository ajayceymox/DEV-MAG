<?php declare(strict_types=1);
/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */
namespace Zazmic\AmazonMCF\Controller\Adminhtml\Shippingrate;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\LayoutFactory;
use Psr\Log\LoggerInterface;
use Magento\Backend\App\Action;
use Magento\Framework\App\RequestInterface as Request;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Zazmic\AmazonMCF\Model\ConfigManager;

class Save extends \Zazmic\AmazonMCF\Controller\Adminhtml\ShippingRate
{
    /**
     * @var \Magento\Framework\App\RequestInterface
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
     * @var ConfigManager
     */
    public $configManager;
  
    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param LayoutFactory $resultLayoutFactory
     * @param LoggerInterface $logger
     * @param Request $request
     * @param PageFactory $resultPageFactory
     * @param RedirectFactory $redirectFactory
     * @param ConfigManager $configManager
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        LayoutFactory $resultLayoutFactory,
        LoggerInterface $logger,
        Request $request,
        PageFactory $resultPageFactory,
        RedirectFactory $redirectFactory,
        ConfigManager $configManager
    ) {
        $this->request               = $request;
        $this->resultPageFactory     = $resultPageFactory;
        $this->resultRedirectFactory = $redirectFactory;
        parent::__construct($context, $coreRegistry, $resultLayoutFactory, $logger, $configManager);
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
        $resultPage->setActiveMenu('Zazmic_AmazonMCF::shipping_rate');
        $postData = $this->request->getPostValue();
        if (isset($postData)) {
           
            $data = [];
            foreach ($postData['shipment_rates'] as $post) {
                $scopeId = $post['scope_id'];
                foreach ($post as $key => $value) {
                    $configPath = '';
                    if ($key != 'scope_id') {
                        $rateType = explode("_", $key);
                        $configPath = 'zazmic/shipping_'.$rateType[0].'/'.$key;
                        $this->configManager->configUpdate(
                            $configPath,
                            $value,
                            ScopeInterface::SCOPE_WEBSITES,
                            $scopeId
                        );
                    }
                }
            }
            $this->messageManager->addSuccess(__('Shipping rates updated'));
          
        } else {
            $this->messageManager->addErrorMessage(__("Failed to save. Please try again"));
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/index', ['_current' => true]);
    }
}
