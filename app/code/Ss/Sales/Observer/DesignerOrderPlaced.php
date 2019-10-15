<?php

namespace Ss\Sales\Observer;

class DesignerOrderPlaced implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Escaper
     */
    protected $_escaper;

    public function __construct(
    \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Escaper $escaper
    )
    {
        $this->_transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->_escaper = $escaper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $themeHelper = $objectManager->create('\Ss\Theme\Helper\Data');

        $order = $observer->getEvent()->getOrder();
        $orderDate = new \DateTime($order->getCreatedAt());

        $arrDesigner = array();
        foreach ($order->getAllItems() as $item) {
            if ($item->getParentItem()) {
                continue;
            }
            $products_designer = $objectManager->create('Magento\Catalog\Model\Product')->load($item['product_id']);
            if ($products_designer->getSsDesigner()) {
                $designerId = $products_designer->getSsDesigner();
                $arrDesigner[$designerId][] = $item;
            }
        }

        foreach ($arrDesigner as $designerId => $designerItems) {
            $productDesigner = $objectManager->create('Ss\Designer\Model\Designer')->load($designerId);

            $receiverInfo = [
                'email' => $themeHelper->getEmailDesigners()
            ];
            $senderInfo = [
                'name' => $themeHelper->getNameGeneral(),
                'email' => $themeHelper->getEmailGeneral()
            ];

            $state_array = $objectManager->create('Magento\Directory\Model\Country')->setId($themeHelper->getCountryId())->getLoadedRegionCollection()->toOptionArray();
            foreach ($state_array as $_state) {
                if ($_state['value'] == $themeHelper->getRegionId()) {
                    $state = $_state['label'];
                }
            }
            $transport = $this->_transportBuilder
                ->setTemplateIdentifier('sales_email_designer_po_template')
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ]
                )
                ->setTemplateVars(
                    [
                        'order' => $order,
                        'state' => $state,
                        'order_date' => $orderDate->format('m/d/Y'),
                        'name' => $productDesigner->getName(),
                        'address' => $productDesigner->getAddress(),
                        'firstname' => $productDesigner->getFirstName(),
                        'lastname' => $productDesigner->getLastName(),
                        'order_note' => $productDesigner->getOrderNote(),
                        'payment_note' => $productDesigner->getPaymentNote(),
                        'item_info' => $this->_getListItems($designerItems),
                    ]
                )
                ->setFrom($senderInfo)
                ->addTo($receiverInfo)
                ->getTransport();

            $transport->sendMessage();
            $this->inlineTranslation->resume();
        }
    }

    /**
     * Get list item
     *
     * @param array $items
     * @return string
     */
    private function _getListItems(array $items)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $priceHelper = $objectManager->create('Magento\Framework\Pricing\Helper\Data');

        $listItem = '';
        $i = 1;
        if (count($items)) {
            foreach ($items as $item) {
                $options = $item['product_options'];
                $style = 'style="font-size: 9px; color: #1d1d1d; font-family: OpenSans-Regular, Verdana, sans-serif; margin: 0";';
                $style_2 = 'style="font-size: 9px; color: #1d1d1d; font-family: OpenSans-Regular, Verdana, sans-serif; margin: 0; line-height: 1.7";';
                $listItem .= "<tr height='20'>
                    <td width='9'></td>
                    <td width='26' align='left' valign='top'><p " . $style_2 . ">" . $i . "</p></td>
                    <td width='55' align='left' valign='top'><p " . $style_2 . ">" . $item['sku'] . "</p></td>
                    <td width='10'></td>
                    <td width='95' align='left' valign='top'><p " . $style_2 . ">" . $item['name'] . "</p></td>
                    <td width='10'></td>
                    <td width='150' align='left' valign='top'><p " . $style_2 . ">";
                $i++;
                if (isset($options['attributes_info'])) {
                    $count = count($options['attributes_info']);
                    foreach ($options['attributes_info'] as $key => $option) {
                        $listItem = ($key != $count - 1) ? ( $listItem . $option['value'] . ", " ) : ( $listItem . $option['value'] ) ;
                    }
                }
                $listItem .= "</p></td>
                    <td width='10'></td>
                    <td width='40' align='left' valign='middle'><p " . $style . ">" . $priceHelper->currency($item['price'], true, false) . "</p></td>
                    <td width='35' align='center' valign='middle'><p " . $style . ">" . $item['qty_ordered'] . "</p></td>
                    <td align='left' valign='middle'><p " . $style . ">" . $priceHelper->currency($item['row_total'], true, false) . "</p></td>
                </tr>";
            }
        }
        return $listItem;
    }

}
