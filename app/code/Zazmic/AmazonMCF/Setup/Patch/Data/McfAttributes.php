<?php declare(strict_types=1);
/**
 * Copyright © 2021 Zazmic. All rights reserved.
 */
declare(strict_types=1);

namespace Zazmic\AmazonMCF\Setup\Patch\Data;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class McfAttributes implements DataPatchInterface
{
   /** @var ModuleDataSetupInterface */
    private $moduleDataSetup;

   /** @var EavSetupFactory */
    private $eavSetupFactory;

   /**
    * @param ModuleDataSetupInterface $moduleDataSetup
    * @param EavSetupFactory $eavSetupFactory
    */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

   /**
    * @inheritdoc
    */
    public function apply()
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'MCF_Enabled');
        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'mcf_enabled');
        $eavSetup->addAttribute(\Magento\Catalog\Model\Product::ENTITY, 'mcf_enabled', [
                'type' => 'int',
                'label' => 'MCF Mapped Status',
                'input' => 'boolean',
                'backend' => '',
                'required' => false,
                'sort_order' => 110,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
                'group' => 'MCF',
                "default" => "0",
                "class" => "",
                "note" => ""
        ]);
    }

   /**
    * @inheritdoc
    */
    public static function getDependencies()
    {
        return [];
    }

   /**
    * @inheritdoc
    */
    public function getAliases()
    {
        return [];
    }

   /**
    * @inheritdoc
    */
    public static function getVersion()
    {
        return '1.0.0';
    }
}
