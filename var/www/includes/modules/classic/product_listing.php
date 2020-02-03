<?php
//MOD Product Listing Sorter 
$debug = '';//1 or 0, show debugging information 
$debug_prefix = '('.str_replace(DIR_FS_CATALOG, '', str_replace('\\','/',__FILE__)).' line ';
/**
 * product_listing module
 *
 * @package modules
 * @copyright Copyright 2003-2011 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: product_listing.php 6787 2007-08-24 14:06:33Z drbyte $
 * UPDATED TO WORK WITH COLUMNAR PRODUCT LISTING For Zen Cart v1.3.6 - 10/25/2006
 */
if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}
// Column Layout Support originally added for Zen Cart v 1.1.4 by Eric Stamper - 02/14/2004
// Upgraded to be compatible with Zen-cart v 1.2.0d by Rajeev Tandon - Aug 3, 2004
// Column Layout Support (Grid Layout) upgraded for v1.3.0 compatibility DrByte 04/04/2006
// Column Layout Support (Grid Layout) upgraded for v1.5.0 compatibility and changed to customer control asarfraz July 26 2012
// Modified for admin control of customer option by Glenn Herbert (gjh42) 2012-09-20   test 20120929 grid sorter
//
if (!defined('PRODUCT_LISTING_LAYOUT_STYLE')) define('PRODUCT_LISTING_LAYOUT_STYLE',(isset($_GET['view']) ? $_GET['view'] : 'rows'));
if (!defined('PRODUCT_LISTING_COLUMNS_PER_ROW')) define('PRODUCT_LISTING_COLUMNS_PER_ROW',3);
if (!defined('PRODUCT_LISTING_GRID_SORT')) define('PRODUCT_LISTING_GRID_SORT',0);
$product_listing_layout_style = isset($_GET['view'])? $_GET['view']: PRODUCT_LISTING_LAYOUT_STYLE;
$row = 0;
$col = 0;
$list_box_contents = array();
$title = '';

// (BOF - 1.3) Set number of products displayed per page (24)
// $max_results = ($product_listing_layout_style=='columns' && PRODUCT_LISTING_COLUMNS_PER_ROW>0) ? (PRODUCT_LISTING_COLUMNS_PER_ROW * (int)(MAX_DISPLAY_PRODUCTS_LISTING/PRODUCT_LISTING_COLUMNS_PER_ROW)) : MAX_DISPLAY_PRODUCTS_LISTING;
$max_results = (PRODUCT_LISTING_LAYOUT_STYLE=='columns' && PRODUCT_LISTING_COLUMNS_PER_ROW>0) ? (PRODUCT_LISTING_COLUMNS_PER_ROW * (int)($_SESSION['product_listing_max_display']/PRODUCT_LISTING_COLUMNS_PER_ROW)) : $_SESSION['product_listing_max_display'];
// (EOF - 1.3) Set number of products displayed per page (24)

$show_submit = zen_run_normal();
$listing_split = new splitPageResults($listing_sql, $max_results, 'p.products_id', 'page');
$zco_notifier->notify('NOTIFY_MODULE_PRODUCT_LISTING_RESULTCOUNT', $listing_split->number_of_rows);
$how_many = 0;

// Begin Row Layout Header
if ($product_listing_layout_style == 'rows' or PRODUCT_LISTING_GRID_SORT) {		// For Column Layout (Grid Layout) add on module

$list_box_contents[0] = array('params' => 'class="productListing-rowheading"');

$zc_col_count_description = 0;
$lc_align = '';
for ($col=0, $n=sizeof($column_list); $col<$n; $col++) {
  switch ($column_list[$col]) {
    case 'PRODUCT_LIST_MODEL':
    $lc_text = TABLE_HEADING_MODEL;
    $lc_align = '';
    $zc_col_count_description++;
    break;
    case 'PRODUCT_LIST_NAME':
    $lc_text = TABLE_HEADING_PRODUCTS;
    $lc_align = '';
    $zc_col_count_description++;
    break;
    case 'PRODUCT_LIST_MANUFACTURER':
    $lc_text = TABLE_HEADING_MANUFACTURER;
    $lc_align = '';
    $zc_col_count_description++;
    break;
    case 'PRODUCT_LIST_PRICE':
    $lc_text = TABLE_HEADING_PRICE;
    $lc_align = 'right' . (PRODUCTS_LIST_PRICE_WIDTH > 0 ? '" width="' . PRODUCTS_LIST_PRICE_WIDTH : '');
    $zc_col_count_description++;
    break;
    case 'PRODUCT_LIST_QUANTITY':
    $lc_text = TABLE_HEADING_QUANTITY;
    $lc_align = 'right';
    $zc_col_count_description++;
    break;
    case 'PRODUCT_LIST_WEIGHT':
    $lc_text = TABLE_HEADING_WEIGHT;
    $lc_align = 'right';
    $zc_col_count_description++;
    break;
    case 'PRODUCT_LIST_IMAGE':
    if ($product_listing_layout_style == 'rows') { //skip if grid
      $lc_text = TABLE_HEADING_IMAGE;
      $lc_align = 'center';
      $zc_col_count_description++;
    }
    break;
  }
if ($debug) echo $debug_prefix.__LINE__.') '.'$_GET[\'sort\']='.$_GET['sort'].'<br />';//steve

  if ( ($column_list[$col] != 'PRODUCT_LIST_IMAGE') ) {
    $lc_text = zen_create_sort_heading($_GET['sort'], $col+1, $lc_text);
  }



  $list_box_contents[0][$col] = array('align' => $lc_align,
                                      'params' => 'class="productListing-heading"',
                                      'text' => $lc_text );
}

  if ($product_listing_layout_style == 'columns') { //grid sort option
    $grid_sort = $list_box_contents[0];
    $list_box_contents = array();
  }
} // End Row Layout Header used in Column Layout (Grid Layout) add on module

/////////////  HEADER ROW ABOVE /////////////////////////////////////////////////

$num_products_count = $listing_split->number_of_rows;

if ($listing_split->number_of_rows > 0) {
  $rows = 0;
  // Used for Column Layout (Grid Layout) add on module
  $column = 0;	
  if ($product_listing_layout_style == 'columns') {
    if ($num_products_count < PRODUCT_LISTING_COLUMNS_PER_ROW || PRODUCT_LISTING_COLUMNS_PER_ROW == 0 ) {
      $col_width = floor(100/$num_products_count) - 0.5;
    } else {
      $col_width = floor(100/PRODUCT_LISTING_COLUMNS_PER_ROW) - 0.5;
    }
  }
  // Used for Column Layout (Grid Layout) add on module


  $listing = $db->Execute($listing_split->sql_query);
  $extra_row = 0;
  while (!$listing->EOF) {
  
    $products_price = zen_get_products_display_price($listing->fields['products_id']);
    if (!isset($productsInCategory[$listing->fields['products_id']])) $productsInCategory[$listing->fields['products_id']] = zen_get_generated_category_path_rev($listing->fields['master_categories_id']);

   $list_box_contents[$row][$col] = array('params' => 'class="centerBoxContentsNew"' . ' ',					   'text' => (($listing->fields['products_image'] == '' and PRODUCTS_IMAGE_NO_IMAGE_STATUS == 0) ? '' : 
					   '<div class="box_image">
								<a href="' . zen_href_link(zen_get_info_page($listing->fields['products_id']), 'cPath=' . $productsInCategory[$listing->fields['products_id']] . '&products_id=' . $listing->fields['products_id']) . '">' . zen_image(DIR_WS_IMAGES . $listing->fields['products_image'], $listing->fields['products_name'], IMAGE_PRODUCT_NEW_WIDTH, IMAGE_PRODUCT_NEW_HEIGHT) . '</a>
					   </div>') .
					   '<div class="product_title">
								<a href="' . zen_href_link(zen_get_info_page($listing->fields['products_id']), 'cPath=' . $productsInCategory[$listing->fields['products_id']] . '&products_id=' . $listing->fields['products_id']) . '">' . $listing->fields['products_name'] . '</a>
					   </div>' . 
					   '<div class="price">'.$products_price.'</div>'.
					   '<div class="product_detail">
								<a href="'. zen_href_link(zen_get_info_page($listing->fields['products_id']), 'cPath=' . $productsInCategory[$listing->fields['products_id']] . '&products_id=' . $listing->fields['products_id']) . '">' .zen_image_button(BUTTON_IMAGE_GOTO_PROD_DETAILS , BUTTON_GOTO_PROD_DETAILS_ALT).'</a>
					    </div>');

    $col ++;
    if ($col > (SHOW_PRODUCT_INFO_COLUMNS_NEW_PRODUCTS - 1)) {
      $col = 0;
      $row ++;
    }
    $listing->MoveNext();
  }
  $error_categories = false;
} else {
  $list_box_contents = array();

  $list_box_contents[0] = array('params' => 'class="productListing-odd"');
  $list_box_contents[0][] = array('params' => 'class="productListing-data"',
                                              'text' => TEXT_NO_PRODUCTS);

  $error_categories = true;
}

if (($how_many > 0 and $show_submit == true and $listing_split->number_of_rows > 0) and (PRODUCT_LISTING_MULTIPLE_ADD_TO_CART == 1 or  PRODUCT_LISTING_MULTIPLE_ADD_TO_CART == 3) ) {
  $show_top_submit_button = true;
} else {
  $show_top_submit_button = false;
}
if (($how_many > 0 and $show_submit == true and $listing_split->number_of_rows > 0) and (PRODUCT_LISTING_MULTIPLE_ADD_TO_CART >= 2) ) {
  $show_bottom_submit_button = true;
} else {
  $show_bottom_submit_button = false;
}



  if ($how_many > 0 && PRODUCT_LISTING_MULTIPLE_ADD_TO_CART != 0 and $show_submit == true and $listing_split->number_of_rows > 0) {
  // bof: multiple products
    echo zen_draw_form('multiple_products_cart_quantity', zen_href_link(FILENAME_DEFAULT, zen_get_all_get_params(array('action')) . 'action=multiple_products_add_product'), 'post', 'enctype="multipart/form-data"');
  }

?>
