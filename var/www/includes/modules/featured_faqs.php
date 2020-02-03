<?php
/**
 * @package FAQ Manager
 * @copyright Copyright 2003-2007 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * based on @version $Id: featured_products.php 6424 2007-05-31 05:59:21Z ajeh $
 * @Original contrib by Vijay Immanuel for osCommerce, converted to zen by dave@open-operations.com - http://www.open-operations.com
 * @featured_faqs.php updated 2012-09-18 to be v1.5 compatible kamelion0927
 */
 if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}
    $title = '';
    $featured_faqs_query = "select distinct p.faqs_id, pd.faqs_name
                           from " . TABLE_FAQS . " p
                           left join " . TABLE_FEATURED_FAQS . " f on p.faqs_id = f.faqs_id
                           left join " . TABLE_FAQS_DESCRIPTION . " pd on p.faqs_id = pd.faqs_id
                           where p.faqs_id = f.faqs_id and p.faqs_id = pd.faqs_id and p.faqs_status = '1' and f.status = '1' and pd.language_id = '" . (int)$_SESSION['languages_id'] . "'";

  $featured_faqs = $db->Execute($featured_faqs_query, MAX_DISPLAY_SEARCH_RESULTS_FEATURED_FAQ);
  $row = 0;
  $col = 0;
  $list_box_contents = '';
  $num_faqs_count = $featured_faqs->RecordCount();

  // show only when 1 or more
    if ($num_faqs_count > 0) {
    while (!$featured_faqs->EOF) {
      $list_box_contents[$row][$col] = array('params' => 'class="featuredFaqsContent"',
                                             'text' => '<a href="' . zen_href_link('faq_info', 'faqs_id=' . $featured_faqs->fields['faqs_id']) . '">' . $featured_faqs->fields['faqs_name'] . '</a>');
      $col ++;
      if ($col > (SHOW_FAQ_INFO_COLUMNS_FEATURED_FAQS - 1)) {
        $col = 0;
        $row ++;
      }
      $featured_faqs->MoveNext();
    }
    if ($featured_faqs->RecordCount() > 0) {
      $zc_show_featured = true;
    }
  }
?>