<?php
namespace Ajay\TestGQ\Model\Resolver\PickUpStores;

/**
 * Interceptor class for @see \Ajay\TestGQ\Model\Resolver\PickUpStores
 */
class Interceptor extends \Ajay\TestGQ\Model\Resolver\PickUpStores implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Ajay\TestGQ\Api\StoreRepositoryInterface $storeRepository, \Magento\Framework\GraphQl\Query\Resolver\Argument\SearchCriteria\Builder $searchCriteriaBuilder)
    {
        $this->___init();
        parent::__construct($storeRepository, $searchCriteriaBuilder);
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
