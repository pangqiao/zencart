<?php
/**
 * Module Template
 *
 * Loaded automatically by index.php?main_page=products_all.<br />
 * Displays listing of All Products
 *
 * @package templateSystem
 * @copyright Copyright 2003-2007 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: tpl_modules_products_all_listing.php 6096 2007-04-01 00:43:21Z ajeh $
 */
?>



<?php
  $group_id = zen_get_configuration_key_value('PRODUCT_ALL_LIST_GROUP_ID');
  
	$row = 0;
	$col = 0;
	$list_box_contents = array();
	$title = '';

  if ($products_all_split->number_of_rows > 0) {
    $products_all = $db->Execute($products_all_split->sql_query);
    $row_counter = 0;
    while (!$products_all->EOF) {
    $products_price = zen_get_products_display_price($products_all->fields['products_id']);
    if (!isset($productsInCategory[$products_all->fields['products_id']])) $productsInCategory[$products_all->fields['products_id']] = zen_get_generated_category_path_rev($products_all->fields['master_categories_id']);

   $list_box_contents[$row][$col] = array('params' => 'class="centerBoxContentsNew"' . ' ',					   'text' => (($products_all->fields['products_image'] == '' and PRODUCTS_IMAGE_NO_IMAGE_STATUS == 0) ? '' : 
					   '<div class="box_image">
								<a href="' . zen_href_link(zen_get_info_page($products_all->fields['products_id']), 'cPath=' . $productsInCategory[$products_all->fields['products_id']] . '&products_id=' . $products_all->fields['products_id']) . '">' . zen_image(DIR_WS_IMAGES . $products_all->fields['products_image'], $products_all->fields['products_name'], IMAGE_PRODUCT_NEW_WIDTH, IMAGE_PRODUCT_NEW_HEIGHT) . '</a>
					   </div>') .
					   '<div class="product_title">
								<a href="' . zen_href_link(zen_get_info_page($products_all->fields['products_id']), 'cPath=' . $productsInCategory[$products_all->fields['products_id']] . '&products_id=' . $products_all->fields['products_id']) . '">' . $products_all->fields['products_name'] . '</a>
					   </div>' . 
					   '<div class="price">'.$products_price.'</div>'.
					   '<div class="product_detail">
								<a href="'. zen_href_link(zen_get_info_page($products_all->fields['products_id']), 'cPath=' . $productsInCategory[$products_all->fields['products_id']] . '&products_id=' . $products_all->fields['products_id']) . '">' .zen_image_button(BUTTON_IMAGE_GOTO_PROD_DETAILS , BUTTON_GOTO_PROD_DETAILS_ALT).'</a>
					    </div>');

    $col ++;
    if ($col > (SHOW_PRODUCT_INFO_COLUMNS_NEW_PRODUCTS - 1)) {
      $col = 0;
      $row ++;
    }
    $products_all->MoveNext();
  }
  } else {
?>
          <tr>
            <td class="main" colspan="2"><?php echo TEXT_NO_ALL_PRODUCTS; ?></td>
          </tr>
<?php
  }
?>
<?php
/**
 * require the list_box_content template to display the products
 */
  require($template->get_template_dir('tpl_columnar_display.php',DIR_WS_TEMPLATE, $current_page_base,'common'). '/tpl_columnar_display.php');
?>
