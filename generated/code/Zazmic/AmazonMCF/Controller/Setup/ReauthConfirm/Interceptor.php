<?php
namespace Zazmic\AmazonMCF\Controller\Setup\ReauthConfirm;

/**
 * Interceptor class for @see \Zazmic\AmazonMCF\Controller\Setup\ReauthConfirm
 */
class Interceptor extends \Zazmic\AmazonMCF\Controller\Setup\ReauthConfirm implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\App\RequestInterface $request, \Magento\Framework\Controller\ResultFactory $resultFactory, \Magento\Framework\Controller\Result\RedirectFactory $redirectFactory, \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList, \Zazmic\AmazonMCF\Model\McfStoreManager $mcfStoreManager, \Magento\Framework\Message\ManagerInterface $messageManager)
    {
        $this->___init();
        parent::__construct($context, $request, $resultFactory, $redirectFactory, $cacheTypeList, $mcfStoreManager, $messageManager);
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
