<?php declare(strict_types=1);
/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */
namespace Zazmic\AmazonMCF\Controller\Adminhtml\Setup;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Cache\TypeListInterface;

class Index extends Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    private $resultPageFactory;
    /**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    private $cacheTypeList;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        TypeListInterface $cacheTypeList
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->cacheTypeList = $cacheTypeList;
        parent::__construct($context);
    }

    /**
     * Execute
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $this->cacheTypeList->cleanType("config");
        $resultPage = $this->getResultPageFactory()->create();
        $resultPage->setActiveMenu('Zazmic_AmazonMCF::setup');
        $resultPage->addBreadcrumb(__('setup'), __('setup'));
        $resultPage->getConfig()->getTitle()->prepend(__('Amazon Multi-Channel Fulfillment (MCF) Setup'));
        return $resultPage;
    }

    /**
     * ResultPageFactory
     *
     * @return \Magento\Framework\View\Result\PageFactory
     */
    public function getResultPageFactory()
    {
        return $this->resultPageFactory;
    }
}
