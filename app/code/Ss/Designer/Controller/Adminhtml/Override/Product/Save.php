<?php

/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Ss\Designer\Controller\Adminhtml\Override\Product;

class Save extends \Magento\Catalog\Controller\Adminhtml\Product\Save
{

    /**
     * @param Action\Context $context
     * @param Builder $productBuilder
     * @param Initialization\Helper $initializationHelper
     * @param \Magento\Catalog\Model\Product\Copier $productCopier
     * @param \Magento\Catalog\Model\Product\TypeTransitionManager $productTypeManager
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     */
    public function __construct(
    \Magento\Backend\App\Action\Context $context,
        \Magento\Catalog\Controller\Adminhtml\Product\Builder $productBuilder,
        \Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper $initializationHelper,
        \Magento\Catalog\Model\Product\Copier $productCopier,
        \Magento\Catalog\Model\Product\TypeTransitionManager $productTypeManager,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
    )
    {

        parent::__construct($context, $productBuilder, $initializationHelper, $productCopier, $productTypeManager, $productRepository);

        $data = $this->getRequest()->getPostValue();
        $designerId = (isset($data["product"][\Ss\Designer\Model\Designer::ATTRIBUTE_CODE])) ? $data["product"][\Ss\Designer\Model\Designer::ATTRIBUTE_CODE] : '';

        if (isset($data["variations-matrix"])) {
            foreach ($data["variations-matrix"] as $key => $value) {
                if (!empty($designerId)) {
                    $value[\Ss\Designer\Model\Designer::ATTRIBUTE_CODE] = $designerId;
                }

                if (isset($data["product"][\Ss\Collection\Model\Collection::ATTRIBUTE_CODE])) {
                    $value[\Ss\Collection\Model\Collection::ATTRIBUTE_CODE] = $data["product"][\Ss\Collection\Model\Collection::ATTRIBUTE_CODE];
                }

                if (isset($data["product"]['estimated_delivery_enable'])) {
                    $value['estimated_delivery_enable'] = $data["product"]['estimated_delivery_enable'];
                    $value['estimated_shipping_enable'] = $data["product"]['estimated_shipping_enable'];
                }

                $value['reference_number'] = $data["product"]['reference_number'];
                $data["variations-matrix"][$key] = $value;
            }

            $this->getRequest()->setPostValue('variations-matrix', $data["variations-matrix"]);
        } elseif (isset($data["configurations"]) && !empty($designerId)) {
            foreach ($data["configurations"] as $productId => $item) {
                $product = $this->productRepository->getById($productId);
                $product->setData(\Ss\Designer\Model\Designer::ATTRIBUTE_CODE, $designerId);
                $product->save();
            }
        }
    }

}
