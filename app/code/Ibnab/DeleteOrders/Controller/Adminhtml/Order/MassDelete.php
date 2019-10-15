<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ibnab\DeleteOrders\Controller\Adminhtml\Order;
//use Magento\Framework\Model\Resource\Db\Collection\AbstractCollection;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Backend\App\Action\Context;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\App\ResourceConnection;
class MassDelete extends \Magento\Sales\Controller\Adminhtml\Order\AbstractMassAction
{
  public $_resource;

  public function __construct(Context $context,
  ResourceConnection $resource,
  Filter $filter, CollectionFactory $collectionFactory)
    {

    $this->_resource = $resource;
    parent::__construct($context , $filter);
    $this->collectionFactory = $collectionFactory;

  }
    /**
     * Cancel selected orders
     *
     * @param AbstractCollection $collection
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    protected function massAction(AbstractCollection $collection)
    {
        $countCancelOrder = 0;
        $connection = $this->_resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        $connectionCheckout = $this->_resource->getConnection('checkout');
        $connectionSales = $this->_resource->getConnection('sales');
        $showTables = $connection->fetchCol('show tables');
        $showTablesSales = $connectionSales->fetchCol('show tables');
        $showTablesCheckout = $connectionCheckout->fetchCol('show tables');

        $showTables = array_merge($showTables, $showTablesSales, $showTablesCheckout);

        //start get table name
        $tblSalesOrder = $connectionSales->getTableName('sales_order');
        $tblSalesCreditmemoComment = $connectionSales->getTableName('sales_creditmemo_comment');
        $tblSalesCreditmemoItem = $connectionSales->getTableName('sales_creditmemo_item');
        $tblSalesCreditmemo = $connectionSales->getTableName('sales_creditmemo');
        $tblSalesCreditmemoGrid = $connectionSales->getTableName('sales_creditmemo_grid');
        $tblSalesInvoiceComment = $connectionSales->getTableName('sales_invoice_comment');
        $tblSalesInvoiceItem = $connectionSales->getTableName('sales_invoice_item');
        $tblSalesInvoice = $connectionSales->getTableName('sales_invoice');
        $tblSalesInvoiceGrid = $connectionSales->getTableName('sales_invoice_grid');

        $tblQuoteAddressItem = $connectionCheckout->getTableName('quote_address_item');
        $tblQuoteItemOption = $connectionCheckout->getTableName('quote_item_option');
        $tblQuote = $connectionCheckout->getTableName('quote');
        $tblQuoteAddress = $connectionCheckout->getTableName('quote_address');
        $tblQuoteItem = $connectionCheckout->getTableName('quote_item');
        $tblQuotePayment = $connectionCheckout->getTableName('quote_payment');
        $tblQuoteShippingRate = $connectionCheckout->getTableName('quote_shipping_rate');
        $tblQuoteIDMask = $connectionCheckout->getTableName('quote_id_mask');

        $tblSalesShipmentComment = $connectionSales->getTableName('sales_shipment_comment');
        $tblSalesShipmentItem = $connectionSales->getTableName('sales_shipment_item');
        $tblSalesShipmentTrack = $connectionSales->getTableName('sales_shipment_track');
        $tblSalesShipment = $connectionSales->getTableName('sales_shipment');
        $tblSalesShipmentGrid = $connectionSales->getTableName('sales_shipment_grid');
        $tblSalesOrderAddress = $connectionSales->getTableName('sales_order_address');
        $tblSalesOrderItem = $connectionSales->getTableName('sales_order_item');
        $tblSalesOrderPayment = $connectionSales->getTableName('sales_order_payment');
        $tblSalesOrderStatusHistory = $connectionSales->getTableName('sales_order_status_history');
        $tblSalesOrderGrid = $connectionSales->getTableName('sales_order_grid');

        $tblLogQuote = $connection->getTableName('log_quote');

        $showTablesLog = $connection->fetchCol('SHOW TABLES LIKE ?', '%'.$tblLogQuote);
        $tblSalesOrderTax = $connectionSales->getTableName('sales_order_tax');

        foreach ($collection->getItems() as $order) {

                $orderId = $order->getId();
                if ($order->getIncrementId()) {
                    $incId = $order->getIncrementId();
                    if (in_array($tblSalesOrder, $showTables)) {
                        $result1 = $connectionSales->fetchAll('SELECT quote_id FROM `'.$tblSalesOrder.'` WHERE entity_id='.$orderId);
                        $quoteId = (int) $result1[0]['quote_id'];
                    }
                    $connection->rawQuery('SET FOREIGN_KEY_CHECKS=1');
                    if (in_array($tblSalesCreditmemoComment, $showTables)) {
                        $connectionSales->rawQuery('DELETE FROM `'.$tblSalesCreditmemoComment.'` WHERE parent_id IN (SELECT entity_id FROM `'.$tblSalesCreditmemo.'` WHERE order_id='.$orderId.')');
                    }
                    if (in_array('sales__creditmemo_item', $showTables)) {
                        $connectionSales->rawQuery('DELETE FROM `'.$tblSalesCreditmemoItem.'` WHERE parent_id IN (SELECT entity_id FROM `'.$tblSalesCreditmemo.'` WHERE order_id='.$orderId.')');
                    }
                    if (in_array($tblSalesCreditmemo, $showTables)) {
                        $connectionSales->rawQuery('DELETE FROM `'.$tblSalesCreditmemo.'` WHERE order_id='.$orderId);
                    }
                    if (in_array($tblSalesCreditmemoGrid, $showTables)) {
                        $connectionSales->rawQuery('DELETE FROM `'.$tblSalesCreditmemoGrid.'` WHERE order_id='.$orderId);
                    }
                    if (in_array($tblSalesInvoiceComment, $showTables)) {
                        $connectionSales->rawQuery('DELETE FROM `'.$tblSalesInvoiceComment.'` WHERE parent_id IN (SELECT entity_id FROM `'.$tblSalesInvoice.'` WHERE order_id='.$orderId.')');
                    }
                    if (in_array($tblSalesInvoiceItem, $showTables)) {
                        $connectionSales->rawQuery('DELETE FROM `'.$tblSalesInvoiceItem.'` WHERE parent_id IN (SELECT entity_id FROM `'.$tblSalesInvoice.'` WHERE order_id='.$orderId.')');
                    }
                    if (in_array($tblSalesInvoice, $showTables)) {
                        $connectionSales->rawQuery('DELETE FROM `'.$tblSalesInvoice.'` WHERE order_id='.$orderId);
                    }
                    if (in_array($tblSalesInvoiceGrid, $showTables)) {
                        $connectionSales->rawQuery('DELETE FROM `'.$tblSalesInvoiceGrid.'` WHERE order_id='.$orderId);
                    }
                    if ($quoteId) {
                        if (in_array($tblQuoteAddressItem, $showTables)) {
                            $connectionCheckout->rawQuery('DELETE FROM `'.$tblQuoteAddressItem.'` WHERE parent_item_id IN (SELECT address_id FROM `'.$tblQuoteAddress.'` WHERE quote_id='.$quoteId.')');
                        }
                        if (in_array($tblQuoteShippingRate, $showTables)) {
                            $connectionCheckout->rawQuery('DELETE FROM `'.$tblQuoteShippingRate.'` WHERE address_id IN (SELECT address_id FROM `'.$tblQuoteAddress.'` WHERE quote_id='.$quoteId.')');
                        }
                       if (in_array($tblQuoteIDMask, $showTables)) {
                           $connectionCheckout->rawQuery('DELETE FROM `'.$tblQuoteIDMask.'` where quote_id='.$quoteId);
                        }
                        if (in_array($tblQuoteItemOption, $showTables)) {
                            $connectionCheckout->rawQuery('DELETE FROM `'.$tblQuoteItemOption.'` WHERE item_id IN (SELECT item_id FROM `'.$tblQuoteItem.'` WHERE quote_id='.$quoteId.')');
                        }
                        if (in_array($tblQuote, $showTables)) {
                            $connectionCheckout->rawQuery('DELETE FROM `'.$tblQuote.'` WHERE entity_id='.$quoteId);
                        }
                        if (in_array($tblQuoteAddress, $showTables)) {
                            $connectionCheckout->rawQuery('DELETE FROM `'.$tblQuoteAddress.'` WHERE quote_id='.$quoteId);
                        }
                        if (in_array($tblQuoteItem, $showTables)) {
                            $connectionCheckout->rawQuery('DELETE FROM `'.$tblQuoteItem.'` WHERE quote_id='.$quoteId);
                        }
                        if (in_array('sales__quotePayment', $showTables)) {
                            $connectionCheckout->rawQuery('DELETE FROM `'.$tblQuotePayment.'` WHERE quote_id='.$quoteId);
                        }
                    }
                    if (in_array($tblSalesShipmentComment, $showTables)) {
                        $connectionSales->rawQuery('DELETE FROM `'.$tblSalesShipmentComment.'` WHERE parent_id IN (SELECT entity_id FROM `'.$tblSalesShipment.'` WHERE order_id='.$orderId.')');
                    }
                    if (in_array($tblSalesShipmentItem, $showTables)) {
                        $connectionSales->rawQuery('DELETE FROM `'.$tblSalesShipmentItem.'` WHERE parent_id IN (SELECT entity_id FROM `'.$tblSalesShipment.'` WHERE order_id='.$orderId.')');
                    }
                    if (in_array($tblSalesShipmentTrack, $showTables)) {
                        $connectionSales->rawQuery('DELETE FROM `'.$tblSalesShipmentTrack.'` WHERE order_id IN (SELECT entity_id FROM `'.$tblSalesShipment.'` WHERE parent_id='.$orderId.')');
                    }
                    if (in_array($tblSalesShipment, $showTables)) {
                        $connectionSales->rawQuery('DELETE FROM `'.$tblSalesShipment.'` WHERE order_id='.$orderId);
                    }
                    if (in_array($tblSalesShipmentGrid, $showTables)) {
                        $connectionSales->rawQuery('DELETE FROM `'.$tblSalesShipmentGrid.'` WHERE order_id='.$orderId);
                    }
                    if (in_array($tblSalesOrder, $showTables)) {
                        $connectionSales->rawQuery('DELETE FROM `'.$tblSalesOrder.'` WHERE entity_id='.$orderId);
                    }
                    if (in_array($tblSalesOrderAddress, $showTables)) {
                        $connectionSales->rawQuery('DELETE FROM `'.$tblSalesOrderAddress.'` WHERE parent_id='.$orderId);
                    }
                    if (in_array($tblSalesOrderItem, $showTables)) {
                        $connectionSales->rawQuery('DELETE FROM `'.$tblSalesOrderItem.'` WHERE order_id='.$orderId);
                    }
                    if (in_array($tblSalesOrderPayment, $showTables)) {
                        $connectionSales->rawQuery('DELETE FROM `'.$tblSalesOrderPayment.'` WHERE parent_id='.$orderId);
                    }
                    if (in_array($tblSalesOrderStatusHistory, $showTables)) {
                        $connectionSales->rawQuery('DELETE FROM `'.$tblSalesOrderStatusHistory.'` WHERE parent_id='.$orderId);
                    }
                    if ($incId && in_array($tblSalesOrderGrid, $showTables)) {
                        $connectionSales->rawQuery('DELETE FROM `'.$tblSalesOrderGrid.'` WHERE increment_id='.$incId);
                    }
                    if (in_array($tblSalesOrderTax, $showTables)) {
                        $connectionSales->rawQuery('DELETE FROM `'.$tblSalesOrderTax.'` WHERE order_id='.$orderId);
                    }
                    if ($quoteId && $showTablesLog) {
                        $connection->rawQuery('DELETE FROM `'.$tblLogQuote.'` WHERE quote_id='.$quoteId);
                    }
                    $connection->rawQuery('SET FOREIGN_KEY_CHECKS=1');
                }

            $countCancelOrder++;
        }
        $countNonCancelOrder = $collection->count() - $countCancelOrder;

        if ($countNonCancelOrder && $countCancelOrder) {
            $this->messageManager->addError(__('%1 order(s) cannot be deleted.', $countNonCancelOrder));
        } elseif ($countNonCancelOrder) {
            $this->messageManager->addError(__('You cannot delete the order(s).'));
        }

        if ($countCancelOrder) {
            $this->messageManager->addSuccess(__('We deleted %1 order(s).', $countCancelOrder));
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('sales/*/');
        return $resultRedirect;
    }
}
