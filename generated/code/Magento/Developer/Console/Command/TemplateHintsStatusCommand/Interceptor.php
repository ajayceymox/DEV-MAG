<?php
namespace Magento\Developer\Console\Command\TemplateHintsStatusCommand;

/**
 * Interceptor class for @see \Magento\Developer\Console\Command\TemplateHintsStatusCommand
 */
class Interceptor extends \Magento\Developer\Console\Command\TemplateHintsStatusCommand implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\Framework\App\Config\ReinitableConfigInterface $reinitableConfig)
    {
        $this->___init();
        parent::__construct($scopeConfig, $reinitableConfig);
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
