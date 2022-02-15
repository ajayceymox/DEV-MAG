<?php

declare(strict_types=1);

namespace Ajay\TestGQ\Model\Resolver;

use Ajay\TestGQ\Model\CreatePickUpStore as CreatPickUpStoreService;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class CreatPickUpStore implements ResolverInterface
{
    /**
     * @var CreatPickUpStoreService
     */
    private $creatPickUpStore;

    /**
     * @param CreatPickUpStore $creatPickUpStore
     */
    public function __construct(CreatPickUpStoreService $creatPickUpStore)
    {
        $this->creatPickUpStore = $creatPickUpStore;
    }

    /**
     * @inheritDoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (empty($args['input']) || !is_array($args['input'])) {
            throw new GraphQlInputException(__('"input" value should be specified'));
        }

        return ['pick_up_store' => $this->creatPickUpStore->execute($args['input'])];
    }
}