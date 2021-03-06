<?php
/**
 * Specials
 *
 * @package page
 * @copyright Copyright 2003-2011 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: main_template_vars.php 18802 2011-05-25 20:23:34Z drbyte $
 */

if (MAX_DISPLAY_SPECIAL_PRODUCTS > 0 ) {
  $specials_query_raw = "SELECT p.products_id, p.products_image, pd.products_name,
                          p.master_categories_id
                         FROM (" . TABLE_PRODUCTS . " p
                         LEFT JOIN " . TABLE_SPECIALS . " s on p.products_id = s.products_id
                         LEFT JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd on p.products_id = pd.products_id )
                         WHERE p.products_id = s.products_id and p.products_id = pd.products_id and p.products_status = '1'
                         AND s.status = 1
                         AND pd.language_id = :languagesID
                         ORDER BY s.specials_date_added DESC";

  $specials_query_raw = $db->bindVars($specials_query_raw, ':languagesID', $_SESSION['languages_id'], 'integer');
  $specials_split = new splitPageResults($specials_query_raw, MAX_DISPLAY_SPECIAL_PRODUCTS);
  $specials = $db->Execute($specials_split->sql_query);
  $row = 0;
  $col = 0;
  $list_box_contents = array();
  $title = '';

  $num_products_count = $specials->RecordCount();
  if ($num_products_count) {
    if ($num_products_count < SHOW_PRODUCT_INFO_COLUMNS_SPECIALS_PRODUCTS || SHOW_PRODUCT_INFO_COLUMNS_SPECIALS_PRODUCTS==0 ) {
      $col_width = floor(100/$num_products_count);
    } else {
      $col_width = floor(100/SHOW_PRODUCT_INFO_COLUMNS_SPECIALS_PRODUCTS);
    }

    $list_box_contents = array();
    while (!$specials->EOF)  {
			$products_price = zen_get_products_display_price($specials->fields['products_id']);
			if (!isset($productsInCategory[$specials->fields['products_id']])) $productsInCategory[$specials->fields['products_id']] = zen_get_generated_category_path_rev($specials->fields['master_categories_id']);

			$list_box_contents[$row][$col] = array('params' => 'class="centerBoxContentsNew"' . ' ',					   'text' => (($specials->fields['products_image'] == '' and PRODUCTS_IMAGE_NO_IMAGE_STATUS == 0) ? '' : 
					   '<div class="box_image">
								<a href="' . zen_href_link(zen_get_info_page($specials->fields['products_id']), 'cPath=' . $productsInCategory[$specials->fields['products_id']] . '&products_id=' . $specials->fields['products_id']) . '">' . zen_image(DIR_WS_IMAGES . $specials->fields['products_image'], $specials->fields['products_name'], IMAGE_PRODUCT_NEW_WIDTH, IMAGE_PRODUCT_NEW_HEIGHT) . '</a>
					   </div>') .
					   '<div class="product_title">
								<a href="' . zen_href_link(zen_get_info_page($specials->fields['products_id']), 'cPath=' . $productsInCategory[$specials->fields['products_id']] . '&products_id=' . $specials->fields['products_id']) . '">' . $specials->fields['products_name'] . '</a>
					   </div>' . 
					   '<div class="price">'.$products_price.'</div>'.
					   '<div class="product_detail">
								<a href="'. zen_href_link(zen_get_info_page($specials->fields['products_id']), 'cPath=' . $productsInCategory[$specials->fields['products_id']] . '&products_id=' . $specials->fields['products_id']) . '">' .zen_image_button(BUTTON_IMAGE_GOTO_PROD_DETAILS , BUTTON_GOTO_PROD_DETAILS_ALT).'</a>
					    </div>');

						$col ++;
						if ($col > (SHOW_PRODUCT_INFO_COLUMNS_NEW_PRODUCTS - 1)) {
						  $col = 0;
						  $row ++;
						}
						$specials->MoveNext();
			}
			require($template->get_template_dir('tpl_specials_default.php',DIR_WS_TEMPLATE, $current_page_base,'templates'). '/tpl_specials_default.php');
  }
}
?>