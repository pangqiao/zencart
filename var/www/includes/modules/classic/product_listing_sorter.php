<?php
/** MOD Product Listing Sorter
 * product_listing_alpha_sorter module
 *
 * @package modules
 * @copyright Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * based on @version $Id: product_listing_alpha_sorter.php 4330 2006-08-31 17:10:26Z ajeh $
 */
if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}

// build sorter dropdown
  if (PRODUCT_LISTING_SORTER == 'true') {

    if ((int)$_GET['product_listing_sorter_id'] == 0) {
      $prefix = TEXT_PRODUCT_LISTING_SORTER_NAMES;
    } else {
      $prefix = TEXT_PRODUCT_LISTING_SORTER_NAMES_RESET;
    }
    $prefix .= ':;';
    $product_sorter_list = explode(';', $prefix . trim(PRODUCT_LISTING_SORTER_LIST));
    for ($j=0, $n=sizeof($product_sorter_list); $j<$n; $j++) {
      $product_sorter_options_list[] = array('id' => $j, 'text' => substr($product_sorter_list[$j], 0, strpos($product_sorter_list[$j], ':')));
    }		

    if (TEXT_PRODUCT_LISTING_SORTER != '') {
	  echo '<label for="productListingSorter" class="inputLabel" style="float:none">' . TEXT_PRODUCT_LISTING_SORTER . '</label>';//css class "inputLabel" floats this text to the left of all the other filter boxes in classic template unless it is cancelled inline
    }
    echo zen_draw_pull_down_menu('product_listing_sorter_id', $product_sorter_options_list, (isset($_GET['product_listing_sorter_id']) ? (int)$_GET['product_listing_sorter_id'] : 0), 'id="productListingSorter" onchange="this.form.submit()" class="subcatogory"');
  }
?>