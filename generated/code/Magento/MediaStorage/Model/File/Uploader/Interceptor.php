<?php
namespace Magento\MediaStorage\Model\File\Uploader;

/**
 * Interceptor class for @see \Magento\MediaStorage\Model\File\Uploader
 */
class Interceptor extends \Magento\MediaStorage\Model\File\Uploader implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct($fileId, \Magento\MediaStorage\Helper\File\Storage\Database $coreFileStorageDb, \Magento\MediaStorage\Helper\File\Storage $coreFileStorage, \Magento\MediaStorage\Model\File\Validator\NotProtectedExtension $validator)
    {
        $this->___init();
        parent::__construct($fileId, $coreFileStorageDb, $coreFileStorage, $validator);
    }

    /**
     * {@inheritdoc}
     */
    public function save($destinationFolder, $newFileName = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'save');
        if (!$pluginInfo) {
            return parent::save($destinationFolder, $newFileName);
        } else {
            return $this->___callPlugins('save', func_get_args(), $pluginInfo);
        }
    }
}
