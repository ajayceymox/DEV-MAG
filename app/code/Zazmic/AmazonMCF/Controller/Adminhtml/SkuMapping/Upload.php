<?php declare(strict_types = 1);
/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */

namespace Zazmic\AmazonMCF\Controller\Adminhtml\SkuMapping;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Zazmic\AmazonMCF\Model\FileUploader;

class Upload extends Action
{
    /**
     * @var FileUploader
     */
    private $fileUploader;
    /**
     * Upload constructor.
     *
     * @param Context $context
     * @param fileUploader $fileUploader
     */
    public function __construct(
        Context $context,
        FileUploader $fileUploader
    ) {
        $this->fileUploader = $fileUploader;
        parent::__construct($context);
    }
    /**
     * Execute function.
     */
    public function execute()
    {
        try {
            $result = $this->fileUploader->saveFileToTmpDir('importdata');
            $result['cookie'] = [
                'name' => $this->_getSession()->getName(),
                'value' => $this->_getSession()->getSessionId(),
                'lifetime' => $this->_getSession()->getCookieLifetime(),
                'path' => $this->_getSession()->getCookiePath(),
                'domain' => $this->_getSession()->getCookieDomain(),
            ];
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}
