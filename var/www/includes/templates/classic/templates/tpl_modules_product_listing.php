<?php
/**
 * Module Template
 *
 * @package templateSystem
 * @copyright Copyright 2003-2005 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: tpl_modules_product_listing.php 3241 2006-03-22 04:27:27Z ajeh $
 * UPDATED TO WORK WITH COLUMNAR PRODUCT LISTING 04/04/2006
 * Modified for admin control of customer option by Glenn Herbert (gjh42) 2012-09-21   2012-11-17 grid sorter
 */
 include(DIR_WS_MODULES . zen_get_module_directory(FILENAME_PRODUCT_LISTING));
?>
<div id="productListing">
<?php
// only show when there is something to submit and enabled
    if ($show_top_submit_button == true) {
?>
<br class="clearBoth" />
<?php
    } // show top submit
?>
<br class="clearBoth" />
<?php if ( ($listing_split->number_of_rows > 0) && ( (PREV_NEXT_BAR_LOCATION == '1') || (PREV_NEXT_BAR_LOCATION == '3') ) ) {
?>
<div id="productsListingTopNumber" class="navSplitPagesResult back"><?php echo $listing_split->display_count(TEXT_DISPLAY_NUMBER_OF_PRODUCTS); ?></div>


<!-- (BOF - 1.3a) Set number of products displayed per page (24) -->
<?php if (PRODUCT_LISTING_DISPLAY_OPTION == 'true' && PRODUCT_LISTING_MAX_DISPLAY_STEPS !='') { ?>
<select onchange="if (this.value) window.location.href=this.value">
<option value="<?php echo TEXT_DISPLAY_STEPS_NUMBER_OF_PRODUCTS_DD; ?>"><?php echo TEXT_DISPLAY_STEPS_NUMBER_OF_PRODUCTS_DD; ?></option>
<?php echo draw_max_ppp($_GET['main_page'], $listing_split->page_name, (int)PRODUCT_LISTING_COLUMNS_PER_ROW, $listing_split->number_of_rows); ?>
</select>
<?php } else { ?>
<div id="maxProductsListingTop" class="navSplitPagesResult back"><?php echo TEXT_DISPLAY_STEPS_NUMBER_OF_PRODUCTS;
echo draw_max_ppp($_GET['main_page'], $listing_split->page_name, (int)PRODUCT_LISTING_COLUMNS_PER_ROW, $listing_split->number_of_rows); ?></div>
<?php } ?>
<!-- (EOF - 1.3a) Set number of products displayed per page (24) -->


<div id="productsListingListingTopLinks" class="navSplitPagesLinks forward"><?php echo TEXT_RESULT_PAGE . ' ' . $listing_split->display_links(MAX_DISPLAY_PAGE_LINKS, zen_get_all_get_params(array('page', 'info', 'x', 'y', 'main_page'))); ?></div>
<br class="clearBoth" />

<?php
}
?>

<?php
/**
 * load the list_box_content template to display the products
 */
if ($product_listing_layout_style == 'columns') {
  if (PRODUCT_LISTING_GRID_SORT) { 
    echo "\n" . '<div id="gridSorter">' . PRODUCT_LISTING_GRID_SORT_TEXT . '<ul>';
    for ($col=0;$col<sizeof($grid_sort);$col++) {
      if ($grid_sort[$col]['text']) echo '<li class="item">' . $grid_sort[$col]['text'] . '</li>';
    }
    echo '</ul></div>' . "\n";
  }
  require($template->get_template_dir('tpl_columnar_display.php',DIR_WS_TEMPLATE, $current_page_base,'common'). '/tpl_columnar_display.php');
} else {// (PRODUCT_LISTING_LAYOUT_STYLE == 'rows')
  require($template->get_template_dir('tpl_tabular_display.php',DIR_WS_TEMPLATE, $current_page_base,'common'). '/tpl_tabular_display.php');
}
?>

<?php if ( ($listing_split->number_of_rows > 0) && ((PREV_NEXT_BAR_LOCATION == '2') || (PREV_NEXT_BAR_LOCATION == '3')) ) {
?>
<div id="productsListingBottomNumber" class="navSplitPagesResult back"><?php echo $listing_split->display_count(TEXT_DISPLAY_NUMBER_OF_PRODUCTS); ?></div>


<!-- (BOF - 1.3a) Set number of products displayed per page (24) -->

<!-- (EOF - 1.3a) Set number of products displayed per page (24) -->


<div  id="productsListingListingBottomLinks" class="navSplitPagesLinks forward"><?php echo TEXT_RESULT_PAGE . ' ' . $listing_split->display_links(MAX_DISPLAY_PAGE_LINKS, zen_get_all_get_params(array('page', 'info', 'x', 'y'))); ?></div>
<br class="clearBoth" />
<?php
  }
?>

<?php
// only show when there is something to submit and enabled
    if ($show_bottom_submit_button == true) {
?>

<br class="clearBoth" />
<?php
    } // show_bottom_submit_button
?>
</div>

<?php
// if ($show_top_submit_button == true or $show_bottom_submit_button == true or (PRODUCT_LISTING_MULTIPLE_ADD_TO_CART != 0 and $show_submit == true and $listing_split->number_of_rows > 0)) {
  if ($show_top_submit_button == true or $show_bottom_submit_button == true) {
?>
</form>
<?php } ?>
