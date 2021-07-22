<?php
/**
 * @package FAQ Manager
 * @copyright Copyright 2003-2009 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * based on @version $Id: default_filter.php 14870 2009-11-19 22:36:24Z drbyte $
 * @Original contrib by Vijay Immanuel for osCommerce, converted to zen by dave@open-operations.com - http://www.open-operations.com
 * @faq_default_filter.php updated 2012-09-18 to be v1.5 compatible kamelion0927
 */
 
if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}
  if (!isset($select_column_list)) $select_column_list = "";

  if (isset($_GET['filter_id']) && zen_not_null($_GET['filter_id']))
      {
      $listing_sql = "select " . $select_column_list . " p.faqs_id, p.faqs_sort_order from " . TABLE_FAQS . " p, " . TABLE_FAQS_DESCRIPTION . " pd, " . TABLE_FAQS_TO_FAQ_CATEGORIES . " p2c where p.faqs_status = 1 and p.faqs_id = p2c.faqs_id and pd.faqs_id = p2c.faqs_id and pd.language_id = '" . (int)$_SESSION['languages_id'] . "' and p2c.faq_categories_id = '" . (int)$current_faq_category_id . "'";
      } else {
      $listing_sql = "select " . $select_column_list . " p.faqs_id, p.faqs_sort_order from " . TABLE_FAQS_DESCRIPTION . " pd, " . TABLE_FAQS . " p, " . TABLE_FAQS_TO_FAQ_CATEGORIES . " p2c where p.faqs_status = 1 and p.faqs_id = p2c.faqs_id and pd.faqs_id = p2c.faqs_id and pd.language_id = '" . (int)$_SESSION['languages_id'] . "' and p2c.faq_categories_id = '" . (int)$current_faq_category_id . "'";
      }

// set the default sort order setting from the Admin when not defined by customer
    if (!isset($_GET['sort']) and FAQ_LISTING_DEFAULT_SORT_ORDER != '') {
      $_GET['sort'] = FAQ_LISTING_DEFAULT_SORT_ORDER;
    }
  if (isset($column_list)) {
    if ((!isset($_GET['sort'])) || (isset($_GET['sort']) && !preg_match('/[1-8][ad]/', $_GET['sort'])) || (substr($_GET['sort'], 0, 1) > sizeof($column_list)) ) {
      for ($i=0, $n=sizeof($column_list); $i<$n; $i++) {
        if (isset($column_list[$i]) && $column_list[$i] == 'FAQ_LIST_NAME') {
          $_GET['sort'] = $i+1 . 'a';
          $listing_sql .= " order by p.faqs_sort_order, pd.faqs_name";
          break;
        } else {
          $listing_sql .= " order by p.faqs_sort_order, pd.faqs_name";
          break;
        }
      }
	  
// if set to nothing use faqs_sort_order and FAQS_LIST_NAME is off
      if (FAQ_LISTING_DEFAULT_SORT_ORDER == '') {
        $_GET['sort'] = '20a';
      }
    } else {
      $sort_col = substr($_GET['sort'], 0 , 1);
      $sort_order = substr($_GET['sort'], 1);
      $listing_sql .= ' order by ';
      switch ($column_list[$sort_col-1]) {
        case 'FAQ_LIST_NAME':
          $listing_sql .= "pd.faqs_name " . ($sort_order == 'd' ? 'desc' : '');
          break;
      }
    }
// optional FAQ List Filter
    if (FAQ_LIST_FILTER > 0){
      $filterlist_sql = "select distinct c.categories_id as id, cd.categories_name as name
      from " . TABLE_FAQS . " p, " .
      TABLE_FAQS_TO_FAQ_CATEGORIES . " p2c, " .
      TABLE_FAQ_CATEGORIES . " c, " .
      TABLE_FAQ_CATEGORIES_DESCRIPTION . " cd
      where p.faqs_status = 1
        and p.faqs_id = p2c.faqs_id
        and p2c.faq_categories_id = c.faq_categories_id
        and p2c.faq_categories_id = cd.faq_categories_id
        and cd.language_id = '" . (int)$_SESSION['languages_id'] . "'
      order by cd.categories_name";
    $do_filter_list = false;
    if ($filterlist->RecordCount() > 1){
        $do_filter_list = true;
        $getoption_set =  true;
        $get_option_variable = 'manufacturers_id';
        $options = array(array('id' => '', 'text' => TEXT_ALL_FAQ_CATEGORIES));
      while (!$filterlist->EOF) {
        $options[] = array('id' => $filterlist->fields['id'], 'text' => $filterlist->fields['name']);
        $filterlist->MoveNext();
      }
    }
  }
