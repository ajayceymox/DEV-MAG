<?php
namespace Zazmic\AmazonMCF\Controller\Adminhtml\SkuMapping\Upload;

/**
 * Interceptor class for @see \Zazmic\AmazonMCF\Controller\Adminhtml\SkuMapping\Upload
 */
class Interceptor extends \Zazmic\AmazonMCF\Controller\Adminhtml\SkuMapping\Upload implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Zazmic\AmazonMCF\Model\FileUploader $fileUploader)
    {
        $this->___init();
        parent::__construct($context, $fileUploader);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'execute');
        if (!$pluginInfo) {
            return parent::execute();
        } else {
            return $this->___callPlugins('execute', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'dispatch');
        if (!$pluginInfo) {
            return parent::dispatch($request);
        } else {
            return $this->___callPlugins('dispatch', func_get_args(), $pluginInfo);
        }
    }
}
