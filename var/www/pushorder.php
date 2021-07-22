<?php
/**
 *
 * @copyright Copyright 2003-2008 Zen Cart Development Team
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: pushorder.php,  v 3.01 2008/05/21 paulm
*/

require('includes/application_top.php');

if (!defined('PAYPAL_PUSHORDER_PASS') || (PAYPAL_PUSHORDER_PASS == '')) { die('PAYPAL_PUSHORDER_PASS is not defined! TIP: read readme.txt!'); }
if (!isset($_POST['secret']) || $_POST['secret'] != PAYPAL_PUSHORDER_PASS) { die('invalid use, ip: ' . $_SERVER['REMOTE_ADDR'] . '(check ip-address and pushorder password settings)'); }
if ((PAYPAL_PUSHORDER_IP != '') && ($_SERVER['REMOTE_ADDR'] != PAYPAL_PUSHORDER_IP)) { die('invalid use, ip: ' . $_SERVER['REMOTE_ADDR'] . '(check ip-address and pushorder password settings)'); }
if(!isset($_POST['id']) || $_POST['id'] == '')  { die('session id not set or empty!'); }

$session_post = zen_db_input($_POST['id']);
$sql = "select * from " . TABLE_PAYPAL_SESSION . " where session_id = '" . zen_db_input($session_post) . "' LIMIT 1";

$stored_session = $db->Execute($sql);
$_SESSION = unserialize(base64_decode($stored_session->fields['saved_session']));

//require(DIR_WS_MODULES . 'require_languages.php');
$current_page_base = 'checkout_process';
//$language_page_directory = 'includes/languages/dutch/'; 
if (!isset($language_page_directory)) $language_page_directory = DIR_WS_LANGUAGES . $_SESSION['language'] . '/';
require(DIR_WS_MODULES . zen_get_module_directory('require_languages.php'));  
/*
echo EMAIL_TEXT_ORDER_NUMBER . '::' . $template_dir_select;
echo '::' . $language_page_directory . $template_dir;
echo '--' . $language_page_directory . $template_dir . '/' . $current_page_base . '.php';
*/
if(!isset($_POST['confirm'])){  
  echo '<form action='. basename($_SERVER['PHP_SELF']) . ' method="POST">' . "\n";
  echo '<input type="hidden" name="secret" value="' . $_POST['secret'] . '">' . "\n";; 
  echo '<input type="hidden" name="id" value="' . $_POST['id']. '">' . "\n";
  echo '<input type="hidden" name="confirm" value="1">' . "\n";
  echo '<input type="submit" value="' . 'Confirm Move to Orders database' . '">' . "\n";
  echo '<input type="checkbox" name="notify" value="true"' . ((PAYPAL_PUSHORDER_SEND_EMAIL == 'true') ? ' checked="checked"' : '') . '> send notification emails' . "\n";   
  echo '</form><br />' . "\n";;

  echo 'Customer name: ' . $_SESSION['customer_first_name'] . '&nbsp;' . $_SESSION['customer_last_name'] . '<br />';
  echo 'zen_id: ' . $_POST['id'];
/*
  echo '<pre>';
  print_r($_SESSION);
  echo '</pre>';
  
  exit();
*/     
}elseif($_POST['confirm'] == '1'){
  // debug
  //print_r($_SESSION);
  //exit();  
  // if the customer is not logged on, redirect them to the time out page
  if (!$_SESSION['customer_id']) {
    zen_redirect(zen_href_link(FILENAME_TIME_OUT));
  }
  
  // add a pushorder comment to the order comments
  $_SESSION['comments'] .= PAYPAL_PUSHORDER_COMMENTS;
 
  // confirm where link came from
  if (!strstr($_SERVER['HTTP_REFERER'], FILENAME_CHECKOUT_CONFIRMATION)) {
  //    zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT,'','SSL'));
  }
  
  // load selected payment module
  require(DIR_WS_CLASSES . 'payment.php');
  $payment_modules = new payment($_SESSION['payment']);
  // load the selected shipping module
  require(DIR_WS_CLASSES . 'shipping.php');
  $shipping_modules = new shipping($_SESSION['shipping']);
  
  require(DIR_WS_CLASSES . 'order.php');
  $order = new order;
  
  // prevent 0-entry orders from being generated/spoofed
  if (sizeof($order->products) < 1) {
    // zen_redirect(zen_href_link(FILENAME_SHOPPING_CART));
  }
  
  require(DIR_WS_CLASSES . 'order_total.php');
  $order_total_modules = new order_total;
  $order_totals = $order_total_modules->pre_confirmation_check();
  $order_totals = $order_total_modules->process();
  
  if (!isset($_SESSION['payment']) && !$credit_covers) {
    //  zen_redirect(zen_href_link(FILENAME_DEFAULT));
  }
  
  // load the before_process function from the payment modules
  //  $payment_modules->before_process();
  
// testing
//exit($session_post);  
  $insert_id = $order->create($order_totals, 2);
  
  $payment_modules->after_order_create($insert_id);
  
  $order->create_add_products($insert_id);
   
  if($_POST['notify'] == 'true'){
    $order->send_order_email($insert_id, 2);
    echo 'Customer notified by email<br />';
  }
  
  echo 'Order id: ' . $insert_id . '<br />';
   
  $db->Execute("DELETE FROM " . TABLE_PAYPAL_SESSION . " where session_id = '" . $session_post . "'");
  echo '(session data is removed from the paypal session table)';
  //Header("Location: /admin/orders.php");
}
?>