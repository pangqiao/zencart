<?php
/**
 * @package FAQ Manager
 * @copyright Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * based on @version $Id: listing_display_order.php 3012 2006-02-11 16:34:02Z wilt $
 * @Original contrib by Vijay Immanuel for osCommerce, converted to zen by dave@open-operations.com - http://www.open-operations.com
 * @faq_listing_display_order.php updated 2012-09-18 to be v1.5 compatible kamelion0927
 */
if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}
if (!isset($_GET['main_page']) || !zen_not_null($_GET['main_page'])) $_GET['main_page'] = 'index';
  if (!isset($_GET['disp_order'])) {
    $_GET['disp_order'] = $disp_order_default;
    $disp_order = $disp_order_default;
  } else {
    $disp_order = $_GET['disp_order'];
  }

  switch (true) {
  case ($_GET['disp_order'] == 0):
  // reset and let reset continue
  $_GET['disp_order'] = $disp_order_default;
  $disp_order = $disp_order_default;
  case ($_GET['disp_order'] == 1):
  $order_by = " order by pd.faqs_name";
  break;
  case ($_GET['disp_order'] == 2):
  $order_by = " order by pd.faqs_name DESC";
  break;
  case ($_GET['disp_order'] == 3):
  $order_by = " order by pc.sort_order, p.faqs_sort_order";
  break;
  case ($_GET['disp_order'] == 6):
  $order_by = " order by p.faqs_date_added DESC, pd.faqs_name";
  break;
  case ($_GET['disp_order'] == 7):
  $order_by = " order by p.faqs_date_added, pd.faqs_name";
  break;
  default:
  $order_by = " order by p.faqs_sort_order";
  break;
  }
?>