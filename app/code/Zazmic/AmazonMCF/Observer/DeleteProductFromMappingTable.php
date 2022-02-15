<?php
declare(strict_types=1);

/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */

namespace Zazmic\AmazonMCF\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Zazmic\AmazonMCF\Api\SkuMappingRepositoryInterface;

class DeleteProductFromMappingTable implements ObserverInterface
{
    /**
     * @var SkuMappingRepositoryInterface
     */
    private $skuMappingRepository;

    /**
     * @param SkuMappingRepositoryInterface $skuMappingRepository
     */
    public function __construct(
        SkuMappingRepositoryInterface $skuMappingRepository
    ) {
        $this->skuMappingRepository = $skuMappingRepository;
    }
    /**
     * Execute
     *
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        $eventProduct = $observer->getEvent()->getProduct();
        $productId = $eventProduct->getId();
        if ($productId) {
            $mappingProduct = $this->skuMappingRepository->getByProductId($productId);
            if (isset($mappingProduct['id'])) {
                $this->skuMappingRepository->deleteById($mappingProduct['id']);
            }
        }
        return $this;
    }
}
