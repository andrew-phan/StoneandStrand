<?php

/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Ss\Checkout\Model\Override\Cart;

use Magento\Quote\Api;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Quote\Model\Cart\Totals\ItemConverter;
use Magento\Quote\Api\CouponManagementInterface;

/**
 * Cart totals data object.
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CartTotalRepository extends \Magento\Quote\Model\Cart\CartTotalRepository
{

    protected $_quoteRepository;
    protected $_itemConverter;
    protected $_designerFactory;
    protected $_productFactory;
    protected $_listDesigners;

    /**
     * @param Api\Data\TotalsInterfaceFactory $totalsFactory
     * @param CartRepositoryInterface $quoteRepository
     * @param DataObjectHelper $dataObjectHelper
     * @param CouponManagementInterface $couponService
     * @param TotalsConverter $totalsConverter
     * @param ItemConverter $converter
     */
    public function __construct(
    \Magento\Quote\Api\Data\TotalsInterfaceFactory $totalsFactory,
        CartRepositoryInterface $quoteRepository,
        DataObjectHelper $dataObjectHelper,
        CouponManagementInterface $couponService,
        \Magento\Quote\Model\Cart\TotalsConverter $totalsConverter,
        ItemConverter $converter,
        \Ss\Designer\Model\DesignerFactory $designerFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory
    )
    {
        parent::__construct($totalsFactory, $quoteRepository, $dataObjectHelper, $couponService, $totalsConverter, $converter);
        $this->_quoteRepository = $quoteRepository;
        $this->_itemConverter = $converter;
        $this->_productFactory = $productFactory;
        $this->_designerFactory = $designerFactory;
    }

    /**
     * {@inheritDoc}
     *
     * @param int $cartId The cart ID.
     * @return Totals Quote totals data.
     */
    public function get($cartId)
    {
        $quoteTotals = parent::get($cartId);
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->_quoteRepository->getActive($cartId);

        $items = [];
        foreach ($quote->getAllVisibleItems() as $index => $item) {
            $items[$index] = $this->_itemConverter->modelToDataObject($item);

            $productId = $quote->getItemById($items[$index]->getItemId())->getProductId();
            $product = $this->_productFactory->create()->load($productId);
            $designerId = $product->getSsDesigner();

            if ($designerId) {
                if (!$this->_listDesigners || !isset($this->_listDesigners[$designerId])) {
                    $designer = $this->_designerFactory->create()->load($designerId);
                    $this->_listDesigners[$designerId] = ($designer->getIsActive()) ? $designer : '';
                }
            } else {
                $designerId = \Ss\Designer\Model\Designer::KEY_NO_DESIGNER;
                $this->_listDesigners[$designerId] = '';
            }

            $items[$index]->setData('designer_name', (empty($this->_listDesigners[$designerId])) ? '' : $this->_listDesigners[$designerId]->getName());
            $items[$index]->setData('designer_url', (empty($this->_listDesigners[$designerId])) ? '' : $this->_listDesigners[$designerId]->getUrl());
        }

        $quoteTotals->setItems($items);

        return $quoteTotals;
    }

}
