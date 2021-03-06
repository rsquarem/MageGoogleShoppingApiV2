<?php

/** @var $installer Zookal_GShoppingV2_Model_Resource_Setup */

$installer = $this;

if ($installer->tableExists('googleshopping_types')) {
    $typesInsert = $installer->getConnection()
        ->select()
        ->from(
            'googleshopping_types',
            [
                'type_id',
                'attribute_set_id',
                'target_country',
            ]
        )
        ->insertFromSelect($installer->getTable('gshoppingv2/types'));

    $itemsInsert = $installer->getConnection()
        ->select()
        ->from(
            'googleshopping_items',
            [
                'item_id',
                'type_id',
                'product_id',
                'gcontent_item_id',
                'store_id',
                'published',
                'expires'
            ]
        )
        ->insertFromSelect($installer->getTable('gshoppingv2/items'));

    $attributes = '';
    foreach (Mage::getModel('gshoppingv2/config')->getAttributes() as $destAttribtues) {
        foreach ($destAttribtues as $code => $info) {
            $attributes .= "'$code',";
        }
    }
    $attributes       = rtrim($attributes, ',');
    $attributesInsert = $installer->getConnection()
        ->select()
        ->from(
            'googleshopping_attributes',
            [
                'id',
                'attribute_id',
                'gcontent_attribute' => new Zend_Db_Expr("IF(gcontent_attribute IN ($attributes), gcontent_attribute, '')"),
                'type_id',
            ]
        )
        ->insertFromSelect($installer->getTable('gshoppingv2/attributes'));

    $installer->run($typesInsert);
    $installer->run($attributesInsert);
    $installer->run($itemsInsert);
}
