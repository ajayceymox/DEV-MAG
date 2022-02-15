<?php
namespace Magento\MediaContentSynchronization\Console\Command\Synchronize;

/**
 * Interceptor class for @see \Magento\MediaContentSynchronization\Console\Command\Synchronize
 */
class Interceptor extends \Magento\MediaContentSynchronization\Console\Command\Synchronize implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\MediaContentSynchronizationApi\Api\SynchronizeInterface $synchronizeContent, \Magento\Framework\App\State $state)
    {
        $this->___init();
        parent::__construct($synchronizeContent, $state);
    }

    /**
     * {@inheritdoc}
     */
    public function run(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface $output)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'run');
        if (!$pluginInfo) {
            return parent::run($input, $output);
        } else {
            return $this->___callPlugins('run', func_get_args(), $pluginInfo);
        }
    }
}
