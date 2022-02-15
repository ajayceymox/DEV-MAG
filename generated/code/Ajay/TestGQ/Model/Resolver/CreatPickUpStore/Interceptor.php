<?php
namespace Ajay\TestGQ\Model\Resolver\CreatPickUpStore;

/**
 * Interceptor class for @see \Ajay\TestGQ\Model\Resolver\CreatPickUpStore
 */
class Interceptor extends \Ajay\TestGQ\Model\Resolver\CreatPickUpStore implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Ajay\TestGQ\Model\CreatePickUpStore $creatPickUpStore)
    {
        $this->___init();
        parent::__construct($creatPickUpStore);
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(\Magento\Framework\GraphQl\Config\Element\Field $field, $context, \Magento\Framework\GraphQl\Schema\Type\ResolveInfo $info, ?array $value = null, ?array $args = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'resolve');
        if (!$pluginInfo) {
            return parent::resolve($field, $context, $info, $value, $args);
        } else {
            return $this->___callPlugins('resolve', func_get_args(), $pluginInfo);
        }
    }
}
