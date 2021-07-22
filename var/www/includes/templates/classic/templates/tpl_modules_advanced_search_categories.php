<?php
/**
 * Module Template
 *
 * Displays content related to "advanced_search_categories"
 *
 * @package templateSystem
 * @copyright Copyright 2003-2005 Ivan Niebla
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: tpl_modules_advanced_search_categories v1.0 2008-10-22 00:00:00
 */
?> 

<h1 id="advSearchResultsDefaultHeading"><?php echo "<br/><br/>".TEXT_DISPLAY_FOUND_CATEGORIES; ?></h1>

<?php

$list_box_contents = '';
$list_box_contents[0] = array('params' => 'class="productListing-rowheading"');

//$column_list_cat = array ('CATEGORY_LIST_IMAGE','CATEGORY_LIST_NAME');

$zc_col_count_description = 0;
$lc_align = '';
for ($col=0, $n=sizeof($column_list_cat); $col<$n; $col++) {
  switch ($column_list_cat[$col]) {
    case 'CATEGORY_LIST_NAME':
    $lc_text = TEXT_HEADING_FOUND_CATEGORIES;
    $lc_align = 'center';
    $zc_col_count_description++;
    break;
    case 'CATEGORY_LIST_IMAGE':
    $lc_text = '';  
    $lc_align = 'left';
    $zc_col_count_description++;
    break;
  }
 
  $list_box_contents[0][$col] = array('align' => $lc_align,
                                      'params' => 'class="productListing-heading"',
                                      'text' => $lc_text );
}


$listing_split = new splitPageResults($listing_sql_cat, MAX_DISPLAY_PRODUCTS_LISTING, 'c.categories_id', 'page');
//$zco_notifier->notify('NOTIFY_MODULE_PRODUCT_LISTING_RESULTCOUNT', $listing_split->number_of_rows);
$how_many = 0;

if ($listing_split->number_of_rows > 0) {
  $rows = 0;
  $listing = $db->Execute($listing_split->sql_query);
  $extra_row = 0;
  while (!$listing->EOF) {
    $rows++;

    if ((($rows-$extra_row)/2) == floor(($rows-$extra_row)/2)) {
      $list_box_contents[$rows] = array('params' => 'class="productListing-even"');
    } else {
      $list_box_contents[$rows] = array('params' => 'class="productListing-odd"');
    }

    $cur_row = sizeof($list_box_contents) - 1;

    for ($col=0, $n=sizeof($column_list_cat); $col<$n; $col++) {
      $lc_align = '';
  
	  $cPath_new = zen_get_generated_category_path_rev($listing->fields['categories_id']);		     
      // strip out 0_ from top level cats
      $cPath_new = str_replace('=0_', '=', $cPath_new);
	  $cPath_new  = "cPath=".$cPath_new;  
	  
      switch ($column_list_cat[$col]) {
        case 'CATEGORY_LIST_NAME':  
		$lc_align = 'left';      
        $lc_text = '<h3 class="itemTitle"><a href="' . zen_href_link(FILENAME_DEFAULT, $cPath_new). '">' . $listing->fields['categories_name'] . '</a></h3><div class="listingDescription">'. zen_trunc_string(zen_clean_html(stripslashes(zen_get_category_description($listing->fields['categories_id'], $_SESSION['languages_id'])))) . '</div>' ;                
        break;
 
        case 'CATEGORY_LIST_IMAGE':
        $lc_align = 'left';
        $lc_text = '<a href="' . zen_href_link(FILENAME_DEFAULT, $cPath_new) . '">';
		$lc_text .= zen_image(DIR_WS_IMAGES . zen_get_categories_image($listing->fields['categories_id']), $listing->fields['categories_name'], IMAGE_PRODUCT_LISTING_WIDTH, IMAGE_PRODUCT_LISTING_HEIGHT) . '</a>'; 
// if ($listing->fields['categories_image'] == '') {
// $lc_text .= '<img src="images/no_picture.jpg" width='.IMAGE_PRODUCT_LISTING_WIDTH.' height='.IMAGE_PRODUCT_LISTING_HEIGHT.'/></a>';
// } else {
// $lc_text .= zen_image(DIR_WS_IMAGES . $listing->fields['categories_image'], $listing->fields['category_name'], IMAGE_PRODUCT_LISTING_WIDTH, IMAGE_PRODUCT_LISTING_HEIGHT, 'class="listingProductImage"') . '</a>';
// }
        break;
      }

      $list_box_contents[$rows][$col] = array('align' => $lc_align,
                                              'params' => 'class="productListing-data"',
                                              'text'  => $lc_text);
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
?>
 
<?php if ( ($listing_split->number_of_rows > 0) && ( (PREV_NEXT_BAR_LOCATION == '1') || (PREV_NEXT_BAR_LOCATION == '3') ) ) {
?>
<div id="productsListingTopNumber" class="navSplitPagesResult back"><?php echo $listing_split->display_count(TEXT_DISPLAY_NUMBER_XX_OF_YY); ?></div>
<div id="productsListingListingTopLinks" class="navSplitPagesLinks forward"><?php echo TEXT_RESULT_PAGE . ' ' . $listing_split->display_links(MAX_DISPLAY_PAGE_LINKS, zen_get_all_get_params(array('page', 'info', 'x', 'y', 'main_page'))); ?></div>
<br class="clearBoth" />
<?php
}
?>

<?php
  require($template->get_template_dir('tpl_tabular_display.php',DIR_WS_TEMPLATE, $current_page_base,'common'). '/tpl_tabular_display.php');
?>

<?php if ( ($listing_split->number_of_rows > 0) && ( (PREV_NEXT_BAR_LOCATION == '1') || (PREV_NEXT_BAR_LOCATION == '3') ) ) {
?>
<div id="productsListingTopNumber" class="navSplitPagesResult back"><?php echo $listing_split->display_count(TEXT_DISPLAY_NUMBER_XX_OF_YY); ?></div>
<div id="productsListingListingTopLinks" class="navSplitPagesLinks forward"><?php echo TEXT_RESULT_PAGE . ' ' . $listing_split->display_links(MAX_DISPLAY_PAGE_LINKS, zen_get_all_get_params(array('page', 'info', 'x', 'y', 'main_page'))); ?></div>
<br class="clearBoth" />
<?php
}
?>