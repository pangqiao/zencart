<?php
/**
 * Page Template
 *
 * @package templateSystem
 * @copyright Copyright 2003-2005 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: tpl_products_all_default.php 2603 2005-12-19 20:22:08Z wilt $
 */
?>
<div class="centerColumn" id="allProductsDefault">

<h1 id="allProductsDefaultHeading"><?php echo HEADING_TITLE; ?></h1>
<br class="clearBoth" />
<?php
require($template->get_template_dir('/tpl_modules_listing_display_order.php',DIR_WS_TEMPLATE, $current_page_base,'templates'). '/tpl_modules_listing_display_order.php'); ?>

<br class="clearBoth" />

<?php
  if (PRODUCT_ALL_LISTING_MULTIPLE_ADD_TO_CART > 0 and $show_submit == true and $products_all_split->number_of_rows > 0) {
?>

<?php
    if ($show_top_submit_button == true or $show_bottom_submit_button == true) {
      echo zen_draw_form('multiple_products_cart_quantity', zen_href_link(FILENAME_PRODUCTS_ALL, zen_get_all_get_params(array('action')) . 'action=multiple_products_add_product'), 'post', 'enctype="multipart/form-data"');
    }
  }
?>

<br class="clearBoth" />

<?php
  if (($products_all_split->number_of_rows > 0) && ((PREV_NEXT_BAR_LOCATION == '1') || (PREV_NEXT_BAR_LOCATION == '3'))) {
?>
  <div id="allProductsListingTopNumber" class="navSplitPagesResult back"><?php echo $products_all_split->display_count(TEXT_DISPLAY_NUMBER_OF_PRODUCTS_ALL); ?></div>
  <div id="allProductsListingTopLinks" class="navSplitPagesLinks forward"><?php echo TEXT_RESULT_PAGE . ' ' . $products_all_split->display_links(MAX_DISPLAY_PAGE_LINKS, zen_get_all_get_params(array('page', 'info', 'x', 'y', 'main_page'))); ?></div>
<?php
  }
?>
<br class="clearBoth" />

<?php
/**
 * display the new products
 */
require($template->get_template_dir('/tpl_modules_products_all_listing.php',DIR_WS_TEMPLATE, $current_page_base,'templates'). '/tpl_modules_products_all_listing.php'); ?>

<?php
  if (($products_all_split->number_of_rows > 0) && ((PREV_NEXT_BAR_LOCATION == '2') || (PREV_NEXT_BAR_LOCATION == '3'))) {
?>
  <div id="allProductsListingBottomNumber" class="navSplitPagesResult back"><?php echo $products_all_split->display_count(TEXT_DISPLAY_NUMBER_OF_PRODUCTS_ALL); ?></div>
  <div id="allProductsListingBottomLinks" class="navSplitPagesLinks forward"><?php echo TEXT_RESULT_PAGE . ' ' . $products_all_split->display_links(MAX_DISPLAY_PAGE_LINKS, zen_get_all_get_params(array('page', 'info', 'x', 'y', 'main_page'))); ?></div>
<?php
  }
?>
<br class="clearBoth" />



<?php
// only end form if form is created
    if ($show_top_submit_button == true or $show_bottom_submit_button == true) {
?>
</form>
<?php } // end if form is made ?>
</div>