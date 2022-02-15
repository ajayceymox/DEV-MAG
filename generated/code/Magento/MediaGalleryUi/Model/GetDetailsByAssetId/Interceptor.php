<?php
namespace Magento\MediaGalleryUi\Model\GetDetailsByAssetId;

/**
 * Interceptor class for @see \Magento\MediaGalleryUi\Model\GetDetailsByAssetId
 */
class Interceptor extends \Magento\MediaGalleryUi\Model\GetDetailsByAssetId implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\MediaGalleryUi\Model\AssetDetailsProviderPool $detailsProviderPool, \Magento\MediaGalleryApi\Api\GetAssetsByIdsInterface $getAssetById, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\MediaGalleryUi\Ui\Component\Listing\Columns\SourceIconProvider $sourceIconProvider, \Magento\MediaGalleryApi\Api\GetAssetsKeywordsInterface $getAssetKeywords)
    {
        $this->___init();
        parent::__construct($detailsProviderPool, $getAssetById, $storeManager, $sourceIconProvider, $getAssetKeywords);
    }

    /**
     * {@inheritdoc}
     */
    public function execute(int $assetId) : array
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'execute');
        if (!$pluginInfo) {
            return parent::execute($assetId);
        } else {
            return $this->___callPlugins('execute', func_get_args(), $pluginInfo);
        }
    }
}
