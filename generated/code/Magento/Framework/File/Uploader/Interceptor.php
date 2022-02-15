<?php
namespace Magento\Framework\File\Uploader;

/**
 * Interceptor class for @see \Magento\Framework\File\Uploader
 */
class Interceptor extends \Magento\Framework\File\Uploader implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct($fileId, ?\Magento\Framework\File\Mime $fileMime = null, ?\Magento\Framework\App\Filesystem\DirectoryList $directoryList = null)
    {
        $this->___init();
        parent::__construct($fileId, $fileMime, $directoryList);
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
