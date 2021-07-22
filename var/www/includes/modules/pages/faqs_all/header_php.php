<?php
/**
 * @package FAQ Module
 * @copyright Copyright 2003-2007 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * based on @version $Id: header_php.php 6912 2007-09-02 02:23:45Z drbyte $
 * @Original contrib by Vijay Immanuel for osCommerce, converted to zen by dave@open-operations.com - http://www.open-operations.com
 * @faqs_all_header.php updated 2012-09-18 to be v1.5 compatible kamelion0927
 */

  require(DIR_WS_MODULES . zen_get_module_directory('require_languages.php'));
  $breadcrumb->add(NAVBAR_TITLE);
  
// display order dropdown
  $disp_order_default = FAQ_SORT_DEFAULT;
  require(DIR_WS_MODULES . zen_get_module_directory(FILENAME_FAQ_LISTING_DISPLAY_ORDER));

    $faqs_all_array = array();

    $faqs_all_query_raw = "select p.*, pd.*, pcd.faq_categories_name from " . TABLE_FAQS . " p, " . TABLE_FAQS_DESCRIPTION . " pd, " . TABLE_FAQ_CATEGORIES_DESCRIPTION . " pcd, " . TABLE_FAQ_CATEGORIES . " pc " .
            "where p.faqs_status = '1' and p.faqs_id = pd.faqs_id and pd.language_id = '" . (int) $_SESSION['languages_id'] . "' and p.master_faq_categories_id = pcd.faq_categories_id and p.master_faq_categories_id = pc.faq_categories_id and pc.faq_categories_status='1' ";
    if((int)$_GET['fcPath'])$faqs_all_query_raw .= "and p.master_faq_categories_id = ".(int)$_GET['fcPath'];
    $faqs_all_query_raw = $faqs_all_query_raw.' '. $order_by;

    $faqs_all_split = new splitPageResults($faqs_all_query_raw, MAX_DISPLAY_FAQS_ALL);

//check to see if we are in normal mode ... not showcase, not maintenance, etc
  $show_submit = zen_run_normal();
?>