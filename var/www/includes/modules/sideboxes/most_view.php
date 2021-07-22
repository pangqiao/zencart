<?php
/**
 * Side Box Template
 *
 * @package templateSystem
 * @copyright Copyright 2003-2005 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: tpl_most_view.php 2982 2006-02-07 07:56:41Z birdbrain $
 * @version $Id: tpl_most_view.php 2982 2007-12-15 21:00:00 TRUST IT - www.trustit.ca - ahmad@trustit.ca $
 * @version $Id: tpl_most_view.php 2982 2008-09-15 21:00:00 Remigio Ruberto - www.100asa.it $
*/

// test if box should display
 $show_most_view= true;
/*
 if (isset($_GET['products_id'])) {
    if (isset($_SESSION['customer_id'])) {
   $check_query = "select count(*) as count
           from " . TABLE_CUSTOMERS_INFO . "
           where customers_info_id = '" . (int)$_SESSION['customer_id'] . "'
           and global_product_notifications = '1'";

   $check = $db->Execute($check_query);

   if ($check->fields['count'] > 0) {
    $show_most_view= true;
   }
  }
 } else {
  $show_most_view= true;
 }
*/
 if ($show_most_view == true) {
  if (isset($current_category_id) && ($current_category_id > 0)) {
   $most_view_query = "select p.products_id, p.products_image, pd.products_viewed, pd.products_name, p.products_price 
              from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, "
                  . TABLE_PRODUCTS_TO_CATEGORIES . " p2c, " . TABLE_CATEGORIES . " c
              where p.products_status = '1'
                            and p.products_id = pd.products_id
              and pd.language_id = '" . (int)$_SESSION['languages_id'] . "'
              and p.products_id = p2c.products_id
              and p2c.categories_id = c.categories_id
              and '" . (int)$current_category_id . "' in (c.categories_id, c.parent_id)
              order by pd.products_viewed desc
              limit 5";


   $most_view = $db->Execute($most_view_query);

  } else {
   $most_view_query = "select p.products_id, p.products_image, pd.products_viewed, pd.products_name,  p.products_price
              from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, "
                  . TABLE_PRODUCTS_TO_CATEGORIES . " p2c, " . TABLE_CATEGORIES . " c
              where p.products_status = '1'
                            and p.products_id = pd.products_id
              and pd.language_id = '" . (int)$_SESSION['languages_id'] . "'
              and p.products_id = p2c.products_id
              and p2c.categories_id = c.categories_id
              and '" . (int)$current_category_id . "' in (c.categories_id, c.parent_id)
              order by pd.products_viewed desc
              limit 5";

   $most_view = $db->Execute($most_view_query);
  }

  if ($most_view->RecordCount() >= MIN_DISPLAY_BESTSELLERS) {
   $title = BOX_HEADING_MOST_VIEW;
   $box_id = bestsellers;
   $rows = 0;
   while (!$most_view->EOF) {
    $rows++;
    $bestsellers_list[$rows]['id'] = $most_view->fields['products_id'];
 $bestsellers_list[$rows]['viewed'] = $most_view->fields['products_viewed'];
    $bestsellers_list[$rows]['name'] = $most_view->fields['products_name'];
	$bestsellers_list[$rows]['image'] = $most_view->fields['products_image'];
    $bestsellers_list[$rows]['price'] = $most_view->fields['products_price'];
    $most_view->MoveNext();
   }

   $left_corner = false;
   $right_corner = false;
   $right_arrow = false;
   $title_link = false;
   require($template->get_template_dir('tpl_most_view.php',DIR_WS_TEMPLATE, $current_page_base,'sideboxes'). '/tpl_most_view.php');
   $title = BOX_HEADING_MOST_VIEW;
   require($template->get_template_dir($column_box_default, DIR_WS_TEMPLATE, $current_page_base,'common') . '/' . $column_box_default);
  }
 }
?>
