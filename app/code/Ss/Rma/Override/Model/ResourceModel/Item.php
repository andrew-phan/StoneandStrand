<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ss\Rma\Override\Model\ResourceModel;

/**
 * RMA entity resource model
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Item extends \Magento\Rma\Model\ResourceModel\Item
{
    const PRODUCT_OPTION_CANT_RETURN = 'Personalize It'; // product option can't return is personalize it
    const PRODUCT_ATTRIBUTE_FINAL_SALE = 'diamond_final_sale'; // product attribute is final sale.
    const PRODUCT_ATTRIBUTE_TRUE = 'Yes'; // product attribute value is turn on.
    
    /**
     * Gets available order items collection
     *
     * @param  int $orderId
     * @param  int|bool $parentId if need retrieves only bundle and its children
     * @return \Magento\Sales\Model\ResourceModel\Order\Item\Collection
     */
    public function getOrderItems($orderId, $parentId = false)
    {
        /** @var $orderItemsCollection \Magento\Sales\Model\ResourceModel\Order\Item\Collection */
        $orderItemsCollection = $this->getOrderItemsCollection($orderId);

        if (!$orderItemsCollection->count()) {
            return $orderItemsCollection;
        }

        /** @var $product \Magento\Catalog\Model\Product */
        $product = $this->_productFactory->create();

        $returnableItems = $this->getReturnableItems($orderId);

        foreach ($orderItemsCollection as $item) {
            /* retrieves only bundle and children by $parentId */
            if ($parentId && $item->getId() != $parentId && $item->getParentItemId() != $parentId) {
                $orderItemsCollection->removeItemByKey($item->getId());
                continue;
            }
            $canReturn = isset($returnableItems[$item->getId()]);
            $canReturnProduct = $this->_rmaData->canReturnProduct($product, $item->getStoreId());
            
            $options = $item->getProductOptions();
            if ($options && isset($options['options'])) {
                foreach ($options['options'] as $option) {
                    if (strtolower($option['label']) == strtolower(static::PRODUCT_OPTION_CANT_RETURN)) {
                        $canReturn = false;
                        break;
                    }
                }
            }
            
            $_product = $item->getProduct();
            if (empty($_product)) {
                $canReturn = false;
            } else {
                $isFinalSale = $_product->getResource()->getAttribute(static::PRODUCT_ATTRIBUTE_FINAL_SALE)->getFrontend()->getValue($_product);
                if($isFinalSale == static::PRODUCT_ATTRIBUTE_TRUE) {
                    $canReturn = false;
                }
            }
            
            if (!$canReturn || !$canReturnProduct) {
                $orderItemsCollection->removeItemByKey($item->getId());
                continue;
            }
            $item->setName($this->getProductName($item));
            $item->setAvailableQty($returnableItems[$item->getId()]);
        }
        return $orderItemsCollection;
    }
}
