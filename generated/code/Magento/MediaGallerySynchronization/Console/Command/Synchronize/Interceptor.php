<?php
namespace Magento\MediaGallerySynchronization\Console\Command\Synchronize;

/**
 * Interceptor class for @see \Magento\MediaGallerySynchronization\Console\Command\Synchronize
 */
class Interceptor extends \Magento\MediaGallerySynchronization\Console\Command\Synchronize implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\MediaGallerySynchronizationApi\Api\SynchronizeInterface $synchronizeAssets, \Magento\Framework\App\State $state)
    {
        $this->___init();
        parent::__construct($synchronizeAssets, $state);
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
