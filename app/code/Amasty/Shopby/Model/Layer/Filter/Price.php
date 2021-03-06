<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2016 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */
namespace Amasty\Shopby\Model\Layer\Filter;

use Amasty\Shopby\Model\Source\DisplayMode;


class Price extends \Magento\CatalogSearch\Model\Layer\Filter\Price
{
    protected $_fromTo;
    protected $minMaxPrice;

    protected $settingHelper;

    protected $currencySymbol;

    public function __construct(
        \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Layer $layer,
        \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder,
        \Magento\Catalog\Model\ResourceModel\Layer\Filter\Price $resource,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Search\Dynamic\Algorithm $priceAlgorithm,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Catalog\Model\Layer\Filter\Dynamic\AlgorithmFactory $algorithmFactory,
        \Magento\Catalog\Model\Layer\Filter\DataProvider\PriceFactory $dataProviderFactory,
        \Amasty\Shopby\Helper\FilterSetting $settingHelper,
        array $data = []
    ) {
        $this->settingHelper = $settingHelper;
        $this->currencySymbol = $priceCurrency->getCurrencySymbol();
        parent::__construct(
            $filterItemFactory, $storeManager, $layer, $itemDataBuilder,
            $resource, $customerSession, $priceAlgorithm, $priceCurrency,
            $algorithmFactory, $dataProviderFactory, $data
        );
    }

    protected function _initItems()
    {
        $filterSetting = $this->settingHelper->getSettingByLayerFilter($this);
        if($filterSetting->getDisplayMode() != DisplayMode::MODE_SLIDER) {
            return parent::_initItems();
        }

        if(!$this->getMinPrice()) {
            return [];
        }

        $this->_items = [
            [
                'from'          => $this->getCurrentFrom(),
                'to'            => $this->getCurrentTo(),
                'min'           => $this->getMinPrice(),
                'max'           => $this->getMaxPrice(),
                'requestVar'    => $this->getRequestVar(),
                'step'          => round($filterSetting->getSliderStep(), 4),
                'template'      => !$filterSetting->getUnitsLabelUseCurrencySymbol() ? '{amount} '.$filterSetting->getUnitsLabel() : $this->currencySymbol . '{amount}',
            ]
        ];
        return $this;
    }


    public function getMinPrice()
    {
        if(is_null($this->minMaxPrice)) {
            $this->initMinMaxPrice();
        }
        return $this->minMaxPrice['min'];
    }

    public function getMaxPrice()
    {
        if(is_null($this->minMaxPrice)) {
            $this->initMinMaxPrice();
        }
        return $this->minMaxPrice['max'];
    }

    public function apply(\Magento\Framework\App\RequestInterface $request)
    {
        $apply = parent::apply($request);
        $filter = $request->getParam($this->getRequestVar());
        if(!empty($filter) && !is_array($filter)) {
            list($from, $to) = explode('-', current(explode(',', $filter)));
            $this->_fromTo['from'] = $from;
            $this->_fromTo['to'] = $to;
        }

        return $apply;
    }


    public function getCurrentFrom()
    {
        return empty($this->_fromTo['from']) ? $this->getMinPrice() : $this->_fromTo['from'];
    }

    public function getCurrentTo()
    {
        return empty($this->_fromTo['to']) ? $this->getMaxPrice() : $this->_fromTo['to'];
    }

    protected function _renderRangeLabel($fromPrice, $toPrice)
    {
        $filterSetting = $this->settingHelper->getSettingByLayerFilter($this);
        if($filterSetting->getUnitsLabelUseCurrencySymbol()) {
            return parent::_renderRangeLabel($fromPrice, $toPrice);
        }
        $formattedFromPrice = round($fromPrice, 4).' '.$filterSetting->getUnitsLabel();
        if ($toPrice === '') {
            return __('%1 and above', $formattedFromPrice);
        } else {
            if ($fromPrice != $toPrice) {
                $toPrice -= .01;
            }
            return __('%1 - %2', $formattedFromPrice, round($toPrice, 4).' '.$filterSetting->getUnitsLabel());
        }
    }

    protected function initMinMaxPrice()
    {
        $collection = clone $this->getLayer()->getProductCollection();
        $this->minMaxPrice = [
            'min' => $collection->getMinPrice(),
            'max' => $collection->getMaxPrice(),
        ];
    }

}
