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
// $Id: WESTERNUNION.php 1000 2009-11-24 06:57:21Z CRYSTAL JONES $
//


  define('MODULE_PAYMENT_WESTERNUNION_TEXT_EMAIL_FOOTER', 
  "IMPORTANT! - Send your Western Union payment to:\n" .
  "\n" . MODULE_PAYMENT_WESTERNUNION_NAME .
  "\n" . MODULE_PAYMENT_WESTERNUNION_ADDRESS . 
  "\n" . MODULE_PAYMENT_WESTERNUNION_CITYSTATEZIP . 
  "\n\nPayments by Western Union may be made in person at a Western Union Agent or online at www.westernunion.com.\n\nYour order will be held until your Western Union payment has been received.\n");

  define('MODULE_PAYMENT_WESTERNUNION_TEXT_TITLE', 'Western Union');
   define('MODULE_PAYMENT_WESTERNUNION_TEXT_DESCRIPTION', 
  '<br />IMPORTANT! - Send your Western Union payment to:<br /><address>' . 
  "\n" . MODULE_PAYMENT_WESTERNUNION_NAME . '<br />' .
  "\n" . MODULE_PAYMENT_WESTERNUNION_ADDRESS . '<br />' . 
  "\n" . MODULE_PAYMENT_WESTERNUNION_CITYSTATEZIP . 
  '</address><p>Payments by Western Union may be made in person at a Western Union Agent or online at www.westernunion.com.<br /><br />Your order will be held until your Western Union payment has been received.');
?>