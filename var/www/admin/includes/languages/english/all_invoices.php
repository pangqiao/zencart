<?php
//
// +----------------------------------------------------------------------+
// |zen-cart Open Source E-commerce                                       |
// +----------------------------------------------------------------------+
// | Copyright (c) 2003 The zen-cart developers                           |
// |                                                                      |
// | http://www.zen-cart.com/index.php                                    |
// |                                                                      |
// | Portions Copyright (c) 2003 osCommerce                               |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.0 of the GPL license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available through the world-wide-web at the following url:           |
// | http://www.zen-cart.com/license/2_0.txt.                             |
// | If you did not receive a copy of the zen-cart license and are unable |
// | to obtain it through the world-wide-web, please send a note to       |
// | license@zen-cart.com so we can mail you a copy immediately.          |
// +----------------------------------------------------------------------+
//  $Id: invoice.php 5961 2007-03-03 17:17:39Z ajeh $
//
// ===============================================================================
// Add-On: All Invoices v1.1
// Designed for: Zen Cart 1.3.x series
// Created by: Mathew O'Marah (www.mdodesign.co.uk)
// -------------------------------------------------------------------------------
// Version 2 modified by: lat9 (lat9@vinosdefrutastropicales.com) for v1.5.0
//
// v2.0.0, 2012-05-18:  Additional language definitions
//
// Donations:  Please support Zen Cart!  paypal@zen-cart.com  - Thank you!
// ===============================================================================

define('HEADING_TITLE', 'All Invoices');

define('TABLE_HEADING_COMMENTS', 'Comments');
define('TABLE_HEADING_CUSTOMER_NOTIFIED', 'Customer Notified');
define('TABLE_HEADING_DATE_ADDED', 'Date Added');
define('TABLE_HEADING_STATUS', 'Status');

define('TABLE_HEADING_QUANTITY', 'Qty');
define('TABLE_HEADING_PRODUCTS_MODEL', 'Model');
define('TABLE_HEADING_PRODUCTS', 'Products');
define('TABLE_HEADING_TAX', 'Tax');
define('TABLE_HEADING_TOTAL', 'Total');
define('TABLE_HEADING_PRICE_EXCLUDING_TAX', 'Price (ex)');
define('TABLE_HEADING_PRICE_INCLUDING_TAX', 'Price');
define('TABLE_HEADING_TOTAL_EXCLUDING_TAX', 'Total (ex)');
define('TABLE_HEADING_TOTAL_INCLUDING_TAX', 'Total');

define('ENTRY_CUSTOMER', 'CUSTOMER:');

define('ENTRY_SOLD_TO', 'SOLD TO:');
define('ENTRY_SHIP_TO', 'SHIP TO:');
define('ENTRY_PAYMENT_METHOD', 'Payment Method:');
define('ENTRY_SUB_TOTAL', 'Sub-Total:');
define('ENTRY_TAX', 'Tax:');
define('ENTRY_SHIPPING', 'Shipping:');
define('ENTRY_TOTAL', 'Total:');
define('ENTRY_DATE_PURCHASED', 'Date Ordered:');

define('ENTRY_ORDER_ID','Invoice No. ');
define('TEXT_INFO_ATTRIBUTE_FREE', '&nbsp;-&nbsp;FREE');
//-bof-a-v2.0.0-lat9
define('TEXT_ALL_INVOICE_INFO', 'This allows you to produce a report from which you can print all invoices for a selected Order Status. The report, which opens in a new tab, displays all the matching invoices in one continuous view but, when printed, each invoice starts on a separate page!');
define('WARNING_NO_ORDERS', '<strong>Note:</strong> There are currently no orders with an order status of <em>%s</em>.');
//-eof-a-v2.0.0