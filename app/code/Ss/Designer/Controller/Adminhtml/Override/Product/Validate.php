<?php

/**
 *
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Ss\Designer\Controller\Adminhtml\Override\Product;

use Magento\Backend\App\Action;
use Magento\Catalog\Controller\Adminhtml\Product;

/**
 * Product validate
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Validate extends \Magento\Catalog\Controller\Adminhtml\Product\Validate
{

    /**
     * @param Action\Context $context
     * @param Builder $productBuilder
     * @param \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter
     * @param \Magento\Catalog\Model\Product\Validator $productValidator
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     */
    public function __construct(
    \Magento\Backend\App\Action\Context $context,
        Product\Builder $productBuilder,
        \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter,
        \Magento\Catalog\Model\Product\Validator $productValidator,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory
    )
    {
        parent::__construct($context, $productBuilder, $dateFilter, $productValidator, $resultJsonFactory, $layoutFactory, $productFactory);

        $data = $this->getRequest()->getPostValue();

        if (isset($data["variations-matrix"])) {
            foreach ($data["variations-matrix"] as $key => $value) {
                if (isset($data["product"][\Ss\Designer\Model\Designer::ATTRIBUTE_CODE])) {
                    $value[\Ss\Designer\Model\Designer::ATTRIBUTE_CODE] = $data["product"][\Ss\Designer\Model\Designer::ATTRIBUTE_CODE];
                }
                if (isset($data["product"][\Ss\Collection\Model\Collection::ATTRIBUTE_CODE])) {
                    $value[\Ss\Collection\Model\Collection::ATTRIBUTE_CODE] = $data["product"][\Ss\Collection\Model\Collection::ATTRIBUTE_CODE];
                }
                if (isset($data["product"]['reference_number'])) {
                    $value['reference_number'] = $data["product"]['reference_number'];
                }
                $data["variations-matrix"][$key] = $value;
            }

            $this->getRequest()->setPostValue('variations-matrix', $data["variations-matrix"]);
        }
    }

}
