<?php
namespace Magento\Framework\Profiler\Driver\Standard\Stat;

/**
 * Interceptor class for @see \Magento\Framework\Profiler\Driver\Standard\Stat
 */
class Interceptor extends \Magento\Framework\Profiler\Driver\Standard\Stat implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct()
    {
        $this->___init();
    }

    /**
     * {@inheritdoc}
     */
    public function start($timerId, $time, $realMemory, $emallocMemory)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'start');
        if (!$pluginInfo) {
            return parent::start($timerId, $time, $realMemory, $emallocMemory);
        } else {
            return $this->___callPlugins('start', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function stop($timerId, $time, $realMemory, $emallocMemory)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'stop');
        if (!$pluginInfo) {
            return parent::stop($timerId, $time, $realMemory, $emallocMemory);
        } else {
            return $this->___callPlugins('stop', func_get_args(), $pluginInfo);
        }
    }
}
