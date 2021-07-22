<?php
/**
 * recent products sidebox
 * includes/modules/sideboxes/YOUR_TEMPLATE/recent_products.php or includes/modules/sideboxes/recent_products.php
  * Updated Harolds 2012-08-29
 */

// test if box should display
if(zen_not_null($_GET['products_id'])) {
	$productid_array = array();
	$product_id1 = $_GET['products_id']; 
	$_SESSION['recent_products1'][] = $product_id1; 
}

	if (isset($_SESSION['recent_products1'])) {
	$productid_array = array_unique(array_reverse($_SESSION['recent_products1'])); 
	
	if (RECENT_VIEWED_PRODUCTS_MAXIMUM < 1) {
	//set the maximum number of recently viewed products here
	 $maximum_recent = 10;
	 } else {
	 $maximum_recent = RECENT_VIEWED_PRODUCTS_MAXIMUM;
	}

	$recent = array_slice($productid_array, 0, $maximum_recent);
	
$sub_query = " where p.products_id  IN (";
foreach($recent as $value){
	$sub_query .= "'" . $value . "', ";
}
$sub_query = substr($sub_query , 0, (strlen($sub_query)-2));
$sub_query .= ")";

  $show_recent_products= true;

  if ($show_recent_products == true) {
  
  $recent_products_query = "select p.products_id, p.products_image, pd.products_name
  					from " . TABLE_PRODUCTS . " as p, " . TABLE_PRODUCTS_DESCRIPTION . " as pd "
					. $sub_query ."
					and p.products_id = pd.products_id
					and pd.language_id = '" . (int)$_SESSION['languages_id'] . "'";
  
  
  
  $recent_products = $db->Execute($recent_products_query);
  
      require($template->get_template_dir('tpl_recent_products.php',DIR_WS_TEMPLATE, $current_page_base,'sideboxes'). '/tpl_recent_products.php');
      $title =  BOX_HEADING_RECENTLY_VIEWED;
      $title_link = '';
      require($template->get_template_dir($column_box_default, DIR_WS_TEMPLATE, $current_page_base,'common') . '/' . $column_box_default);
  }
  }
 
  
?>
