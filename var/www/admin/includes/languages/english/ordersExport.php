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
// +----------------------------------------------------------------------+
// | export of orders is based on easypopulate module 2005 by langer      |
// +----------------------------------------------------------------------+
// $Id: attribExport.php,v0.1 2007 matej $
//

//where to safe the file on the server
define('ORDERSEXPORT_CONFIG_TEMP_DIR', 'oexport/');

// $messageStack
// file handling - msg stack alerts - output via $messageStack
define('ORDERSEXPORT_MSGSTACK_FILE_EXPORT_SUCCESS', 'File <b>%s.txt</b> successfully exported! The file is ready for FTP download in your /%s directory.');
//Error message from the mysql syntax test

define('ORDERSEXPORT_MSGSTACK_ERROR_EXISTS','That\'s unlucky, see below... ');
define('ORDERSEXPORT_MSGSTACK_ERROR_SQL','There is an error in one of the sql statements - %s');
define('ORDERSEXPORT_MSGSTACK_ERROR_DLTYPE','Download type was not correctly specified!');

// admin page definitions
define('ORDERSEXPORT_PAGE_HEADING', 'Orders Exporter');

define('ORDERSEXPORT_PAGE_HEADING2', 'Download Orders - tab-delimited .txt file');
define('ORDERSEXPORT_LINK_DOWNLOAD1', '...all orders full export');
define('ORDERSEXPORT_LINK_DOWNLOAD1B', '...all orders full export WITHOUT DELIVERED ORDERS');
define('ORDERSEXPORT_LINK_DOWNLOAD2', '...all ordered products  WITHOUT DELIVERED ORDERS (attributes excluded)');
define('ORDERSEXPORT_LINK_DOWNLOAD3', '...ordered products with attributes (only) WITHOUT DELIVERED ORDERS');

define('ORDERSEXPORT_PAGE_HEADING3', 'Save Orders in /oexport/ directory on the server - tab-delimited .txt file');
define('ORDERSEXPORT_LINK_SAVE1', '...all orders full export');
define('ORDERSEXPORT_LINK_SAVE1B', '...all orders full export WITHOUT DELIVERED ORDERS');
define('ORDERSEXPORT_LINK_SAVE2', '...all ordered products WITHOUT DELIVERED ORDERS (attributes excluded)');
define('ORDERSEXPORT_LINK_SAVE3', '...ordered products with attributes (only) WITHOUT DELIVERED ORDERS');

define('ORDERSEXPORT_VERSION', 'Orders Export Version:');
?>