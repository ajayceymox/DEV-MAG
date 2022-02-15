<?php declare(strict_types=1);
/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */
namespace Zazmic\AmazonMCF\Controller\Setup;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface as Request;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\App\Cache\TypeListInterface;
use Zazmic\AmazonMCF\Model\McfStoreManager;

class Status extends Action
{
    /**
     * @var Request
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
     * @param Context $context
     * @param Request $request
     * @param PageFactory $resultPageFactory
     * @param TypeListInterface $cacheTypeList
     */
    public function __construct(
        Context $context,
        Request $request,
        PageFactory $resultPageFactory,
        TypeListInterface $cacheTypeList
    ) {
        $this->request = $request;
        $this->resultPageFactory = $resultPageFactory;
        $this->cacheTypeList = $cacheTypeList;
        parent::__construct($context);
    }
    /**
     * Excute

     * @return \Magento\Framework\Controller\Result\RedirectFactory
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->cacheTypeList->cleanType("config");
        return $resultPage;
    }
}
