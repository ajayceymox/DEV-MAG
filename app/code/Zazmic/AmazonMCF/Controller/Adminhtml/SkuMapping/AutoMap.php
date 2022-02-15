<?php declare(strict_types=1);
/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */
namespace Zazmic\AmazonMCF\Controller\Adminhtml\SkuMapping;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\LayoutFactory;
use Magento\Framework\View\Result\PageFactory;
use Zazmic\AmazonMCF\Model\ConfigManager;
use Psr\Log\LoggerInterface;
use Zazmic\AmazonMCF\Model\McfLogManager;

class AutoMap extends \Zazmic\AmazonMCF\Controller\Adminhtml\SkuMapping
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;
    /**
     * @var Context
     */
    private $context;
    /**
     * @var Registry
     */
    private $coreRegistry;
    /**
     * @var LayoutFactory
     */
    private $resultLayoutFactory;
    /**
     * @var ConfigManager
     */
    private $configManager;
    /**
     * @var PageFactory
     */
    private $resultPageFactory;
    /**
     * @var McfLogManager
     */
    private $mcfLogManager;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param LayoutFactory $resultLayoutFactory
     * @param LoggerInterface $logger
     * @param PageFactory $resultPageFactory
     * @param ConfigManager $configManager
     * @param McfLogManager $mcfLogManager
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        LayoutFactory $resultLayoutFactory,
        LoggerInterface $logger,
        PageFactory $resultPageFactory,
        ConfigManager $configManager,
        McfLogManager $mcfLogManager
    ) {
        $this->configManager = $configManager;
        $this->resultPageFactory = $resultPageFactory;
        $this->mcfLogManager = $mcfLogManager;
        parent::__construct($context, $coreRegistry, $resultLayoutFactory, $logger, $configManager);
    }

    /**
     * Sku Mapping index action
     *
     * @return \Magento\Backend\Model\View\Result\Forward
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Zazmic_AmazonMCF::sku_mapping');
        $resultPage->getConfig()->getTitle()->prepend(__('SKU Mapping'));

        try {
            $mcfItems = $this->configManager->getMcfRequest('inventory');
            if ($mcfItems != 404) {
                $i = 0;
                $j = 0;
                $k = 0;
                $msg = "";
                $errArr = [];
                $skuArr = [];
                foreach ($mcfItems['payload']['inventorySummaries'] as $item) {
                    $mapArr['asin'] = $item['asin'];
                    $mapArr['seller_sku'] = $item['sellerSku'];
                    $mapArr['product_name'] = $item['productName'];
                    $result = $this->configManager->skuMap($mapArr);
                    $i++;
                    if (isset($result['status'])) {
                        if ($result['status'] == 'saved') {
                            $skuArr[] = $mapArr['seller_sku'];
                            $j++;
                        }
                        if ($result['status'] == 'updated') {
                            $skuArr[] = $mapArr['seller_sku'];
                            $k++;
                        }
                        if ($result['status'] == 'error') {
                            $errArr = $result['msg'];
                        }
                        $i++;
                    }
                }
                $skuArr = implode(", ", $skuArr);                
                $errArr = implode(",", $errArr);
                if ($errArr != '') {
                    $msg = $errArr.' already mapped with other products';
                    $logData = [
                        'area' => __('AutoMap SKU'),
                        'type' => __('Error'),
                        'details' => $msg,
                    ];    
                }
                $this->configManager->syncInfoUpdate('skumapping', $i);
                $this->messageManager->addSuccessMessage(__("%1 new SKUs mapped and %2 SKUs were updated", $j, $k));
                $msg = $j.' new SKUs mapped and '.$k.' SKUs were updated. SKUs are <b>'.$skuArr.'</b>';
                $logData = [
                    'area' => __('AutoMap SKU'),
                    'type' => __('Info'),
                    'details' => $msg,
                ];    
            } else {
                $this->messageManager->addErrorMessage(__("Failed to map SKUs due to invalid server response."));
                $logData = [
                    'area' => __('AutoMap SKU'),
                    'type' => __('Error'),
                    'details' => __("Failed to map SKUs due to invalid server response."),
                ];    
            }
        } catch (\RuntimeException $e) {
            $logData = [
                'area' => __('AutoMap SKU'),
                'type' => __('Error'),
                'details' => __("Failed to map SKUs automatically."),
            ];    
            $this->messageManager->addErrorMessage(__("Failed to map SKUs automatically."));
        } catch (\Exception $e) {
            $logData = [
                'area' => __('AutoMap SKU'),
                'type' => __('Error'),
                'details' => __("Failed to map SKUs automatically due to invalid server response."),
            ];    
            $this->messageManager->addErrorMessage(__("Failed to map SKUs automatically due to invalid server response."));
        } catch (LocalizedException $e) {
            $logData = [
                'area' => __('AutoMap SKU'),
                'type' => __('Error'),
                'details' => __("Failed to map SKUs automatically."),
            ];    
            $this->messageManager->addErrorMessage(__("Failed to map SKUs automatically."));
        }
        if (isset($logData)) {
            $this->mcfLogManager->addLog($logData);
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('amazonmcf/skumapping/index', ['_current' => true]);
    }
}
