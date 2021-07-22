<?php
/**
 * 
 * init_products_per_page.php
 *
 * @package initSystem
 * @copyright Copyright 2003-2011 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * Added by rbarbour (ZCAdditions.com), Set Number Of Products Per Page (24)
 */
if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}

if(isset($_POST['max_display']) || isset($_GET['max_display'])) {
$_SESSION['product_listing_max_display'] = (int)$_REQUEST['max_display'];
} elseif (!isset($_SESSION['product_listing_max_display'])) {
$_SESSION['product_listing_max_display'] = (int)MAX_DISPLAY_PRODUCTS_LISTING;
}
?>