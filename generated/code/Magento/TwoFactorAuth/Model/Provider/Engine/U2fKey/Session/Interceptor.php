<?php
namespace Magento\TwoFactorAuth\Model\Provider\Engine\U2fKey\Session;

/**
 * Interceptor class for @see \Magento\TwoFactorAuth\Model\Provider\Engine\U2fKey\Session
 */
class Interceptor extends \Magento\TwoFactorAuth\Model\Provider\Engine\U2fKey\Session implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Request\Http $request, \Magento\Framework\Session\SidResolverInterface $sidResolver, \Magento\Framework\Session\Config\ConfigInterface $sessionConfig, \Magento\Framework\Session\SaveHandlerInterface $saveHandler, \Magento\Framework\Session\ValidatorInterface $validator, \Magento\Framework\Session\StorageInterface $storage, \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager, \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory, \Magento\Framework\App\State $appState, ?\Magento\Framework\Session\SessionStartChecker $sessionStartChecker = null)
    {
        $this->___init();
        parent::__construct($request, $sidResolver, $sessionConfig, $saveHandler, $validator, $storage, $cookieManager, $cookieMetadataFactory, $appState, $sessionStartChecker);
    }

    /**
     * {@inheritdoc}
     */
    public function start()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'start');
        if (!$pluginInfo) {
            return parent::start();
        } else {
            return $this->___callPlugins('start', func_get_args(), $pluginInfo);
        }
    }
}
