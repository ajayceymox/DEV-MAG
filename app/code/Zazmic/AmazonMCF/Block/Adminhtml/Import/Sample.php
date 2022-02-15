<?php declare(strict_types=1);
/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */
namespace Zazmic\AmazonMCF\Block\Adminhtml\Import;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Backend\Block\Template;

/**
 * Import Sample class
 *
 */
class Sample extends Template
{
    /**
     * @var Template $_template
     */
    protected $_template = 'sample.phtml';
}
