<?php declare(strict_types=1);
/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */
namespace Zazmic\AmazonMCF\Controller\Setup;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\RequestInterface as Request;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\App\Cache\TypeListInterface;
use Zazmic\AmazonMCF\Model\McfStoreManager;

class ReauthConfirm extends Action
{
    /**
     * @var Request
     */
    private $request;
    /**
     * @var ResultFactory
     */
    protected $resultFactory;
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
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @param Context $context
     * @param Request $request
     * @param ResultFactory $resultFactory
     * @param RedirectFactory $redirectFactory
     * @param TypeListInterface $cacheTypeList
     * @param McfStoreManager $mcfStoreManager
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        Context $context,
        Request $request,
        resultFactory $resultFactory,
        RedirectFactory $redirectFactory,
        TypeListInterface $cacheTypeList,
        McfStoreManager $mcfStoreManager,
        ManagerInterface $messageManager
    ) {
        $this->request = $request;
        $this->resultFactory = $resultFactory;
        $this->redirectFactory = $redirectFactory;
        $this->cacheTypeList = $cacheTypeList;
        $this->mcfStoreManager = $mcfStoreManager;
        $this->messageManager = $messageManager;
        parent::__construct($context);
    }
    /**
     * Excute

     * @return \Magento\Framework\Controller\Result\RedirectFactory
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $data = $this->request->getParams();
        if ($data) {
            try {
                if (!empty($data['selling_partner_id']) &&
                    !empty($data['spapi_oauth_code'])) {
                    if ($this->mcfStoreManager->addAmcfStore($data) == true) {
                        $this->messageManager->addSuccessMessage(__("Connected successfully.Please click to 
                        close the window"));
                    } else {
                        $this->messageManager->addErrorMessage(__("Something went wrong while connecting the store"));
                    }
                } else {
                    $this->messageManager->addErrorMessage(__("There is something wrong to connect"));
                }
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __($e->getMessage())
                );
            }
        } else {
            $this->messageManager->addError(__("Invalid response data from the Amazon server"));
        }
        $this->cacheTypeList->cleanType("config");
        $redirect = $this->redirectFactory->create();
        $redirect->setPath('amazonmcf/setup/status');
        return $redirect;
    }
}
