<?php
/**
 * Module Template
 *
 * Loaded automatically by index.php?main_page=featured_products.<br />
 * Displays listing of Featured Products
 *
 * @package templateSystem
 * @copyright Copyright 2003-2007 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: tpl_modules_products_featured_listing.php 6096 2007-04-01 00:43:21Z ajeh $
 */
?>

<?php

$row = 0;
$col = 0;
$list_box_contents = array();
$title = '';

  $group_id = zen_get_configuration_key_value('PRODUCT_FEATURED_LIST_GROUP_ID');

  if ($featured_products_split->number_of_rows > 0) {
    $featured_products = $db->Execute($featured_products_split->sql_query);

    while (!$featured_products->EOF) {
							  $products_price = zen_get_products_display_price($featured_products->fields['products_id']);
    if (!isset($productsInCategory[$featured_products->fields['products_id']])) $productsInCategory[$featured_products->fields['products_id']] = zen_get_generated_category_path_rev($products_new->fields['master_categories_id']);

			$list_box_contents[$row][$col] = array('params' => 'class="centerBoxContentsNew"' . ' ',					   'text' => (($featured_products->fields['products_image'] == '' and PRODUCTS_IMAGE_NO_IMAGE_STATUS == 0) ? '' : 
					   '<div class="box_image">
								<a href="' . zen_href_link(zen_get_info_page($featured_products->fields['products_id']), 'cPath=' . $productsInCategory[$featured_products->fields['products_id']] . '&products_id=' . $featured_products->fields['products_id']) . '">' . zen_image(DIR_WS_IMAGES . $featured_products->fields['products_image'], $featured_products->fields['products_name'], IMAGE_PRODUCT_NEW_WIDTH, IMAGE_PRODUCT_NEW_HEIGHT) . '</a>
					   </div>') .
					   '<div class="product_title">
								<a href="' . zen_href_link(zen_get_info_page($featured_products->fields['products_id']), 'cPath=' . $productsInCategory[$featured_products->fields['products_id']] . '&products_id=' . $featured_products->fields['products_id']) . '">' . $featured_products->fields['products_name'] . '</a>
					   </div>' . 
					   '<div class="price">'.$products_price.'</div>'.
					   '<div class="product_detail">
								<a href="'. zen_href_link(zen_get_info_page($featured_products->fields['products_id']), 'cPath=' . $productsInCategory[$featured_products->fields['products_id']] . '&products_id=' . $featured_products->fields['products_id']) . '">' .zen_image_button(BUTTON_IMAGE_GOTO_PROD_DETAILS , BUTTON_GOTO_PROD_DETAILS_ALT).'</a>
					    </div>');

						$col ++;
						if ($col > (SHOW_PRODUCT_INFO_COLUMNS_NEW_PRODUCTS - 1)) {
						  $col = 0;
						  $row ++;
						}
						$featured_products->MoveNext();
			}
  } else {
?>
          <tr>
            <td class="main" colspan="2"><?php echo TEXT_NO_FEATURED_PRODUCTS; ?></td>
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

