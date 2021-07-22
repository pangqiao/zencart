<?php
/**
 * @package FAQ Manager
 * @copyright Copyright 2003-2005 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * based on @version $Id: categories.php 2718 2005-12-28 06:42:39Z drbyte $
 * @Original contrib by Vijay Immanuel for osCommerce, converted to zen by dave@open-operations.com - http://www.open-operations.com
 * @faq_categories.php updated 2012-09-18 to be v1.5 compatible kamelion0927
 */

  $show_faq_categories = true;
 if ($_GET['main_page']=='faqs_all' or $_GET['main_page']=='faq_info')  {
  // show it
  $show_faq_categories = true;
} else {
  // do not show it
  $show_faq_categories = false;
}  

  if ($show_faq_categories == true) {
    $main_faq_category_tree = new faq_category_tree;
    $row = 0;
    $box_faq_categories_array = array();
// don't build a tree when no faq_categories
    $check_faq_categories = $db->Execute("select faq_categories_id from " . TABLE_FAQ_CATEGORIES . " where faq_categories_status=1 limit 1");
    if ($check_faq_categories->RecordCount() > 0) {
      $box_faq_categories_array = $main_faq_category_tree->zen_faq_category_tree();
    }
    require($template->get_template_dir('tpl_faq_categories.php',DIR_WS_TEMPLATE, $current_page_base,'sideboxes'). '/tpl_faq_categories.php');
    $title = BOX_HEADING_FAQ_CATEGORIES;
    $title_link = FILENAME_FAQS;
    require($template->get_template_dir($column_box_default, DIR_WS_TEMPLATE, $current_page_base,'common') . '/' . $column_box_default);
}
?>