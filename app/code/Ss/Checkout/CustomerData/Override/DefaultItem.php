<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Ss\Checkout\CustomerData\Override;

/**
 * Default item reder in minicart
 */
class DefaultItem extends \Magento\Checkout\CustomerData\DefaultItem
{

    protected $_productFactory;
    protected $_designerFactory;
    protected $_listDesigners;

    /**
     * 
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Magento\Msrp\Helper\Data $msrpHelper
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\Catalog\Helper\Product\ConfigurationPool $configurationPool
     * @param \Magento\Checkout\Helper\Data $checkoutHelper
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Ss\Designer\Model\DesignerFactory $designerFactory
     * @codeCoverageIgnore
     */
    public function __construct(
    \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Msrp\Helper\Data $msrpHelper,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Catalog\Helper\Product\ConfigurationPool $configurationPool,
        \Magento\Checkout\Helper\Data $checkoutHelper,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Ss\Designer\Model\DesignerFactory $designerFactory
    )
    {
        parent::__construct($imageHelper, $msrpHelper, $urlBuilder, $configurationPool, $checkoutHelper);
        $this->_productFactory = $productFactory;
        $this->_designerFactory = $designerFactory;
    }

    /**
     * {@inheritdoc}
     */
    protected function doGetItemData()
    {
        $results = parent::doGetItemData();
        $results['designer'] = $this->getDesignerProduct();
        return $results;
    }

    /**
     * @todo to get Designer product.
     * @return type
     */
    protected function getDesignerProduct()
    {
        $product = $this->item->getProduct();
        $designerId = $product->getSsDesigner();
        if (is_null($designerId)) {
            $product = $this->_productFactory->create()->load($product->getId());
            $designerId = $product->getSsDesigner();
        }

        if ($designerId) {
            if (!$this->_listDesigners || !isset($this->_listDesigners[$designerId])) {
                $designer = $this->_designerFactory->create()->load($designerId);
                $this->_listDesigners[$designerId] = ($designer->getIsActive()) ? $designer : '';
            }
        } else {
            $designerId = \Ss\Designer\Model\Designer::KEY_NO_DESIGNER;
            $this->_listDesigners[$designerId] = '';
        }

        return [
            'id' => $designerId,
            'name' => (empty($this->_listDesigners[$designerId])) ? '' : $this->_listDesigners[$designerId]->getName(),
            'url' => (empty($this->_listDesigners[$designerId])) ? '' : $this->_listDesigners[$designerId]->getUrl()
        ];
    }

}
