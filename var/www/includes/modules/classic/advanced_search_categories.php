<?php
/**
 * advanced_search_categories.php module
 *
 * @package modules
 * @copyright Copyright 2003-2007 Zen Cart Development Team
 * @copyright Iniebla Copyright 2008 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: advanced_search_categories.php  v1.0 2008-10-30 00:00:00 by iniebla $
 */
if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}

$define_list_cat = array('CATEGORY_LIST_NAME' => CATEGORY_LIST_NAME,
                     'CATEGORY_LIST_IMAGE' => CATEGORY_LIST_IMAGE);
// Close to how it is used in /pages/advanced_search_results/header_php.php:  table with fields to be selected and displayed (besides others).  
// It will be used as $column_list_cat by tpl_modules_advanced_search_categories.php
//

asort($define_list_cat);

$column_list_cat = array();
reset($define_list_cat);
while (list($column, $value) = each($define_list_cat)) {
  if ($value) $column_list_cat[] = $column;
}

$select_column_list_cat = '';

for ($col=0, $n=sizeof($column_list_cat); $col<$n; $col++) {
  if (zen_not_null($select_column_list_cat)) {
    $select_column_list_cat .= ', ';
  }
  switch ($select_column_list_cat[$col]) {
    case 'CATEGORY_LIST_NAME':
    $select_column_list_cat .= 'cd.categories_name';
    break;
    case 'CATEGORY_LIST_IMAGE':
    $select_column_list_cat .= 'c.categories_image';
    break;
  }
}

$select_str_cat = "SELECT DISTINCT " . $select_column_list_cat . "cd.categories_description, cd.categories_id, c.parent_id ";

$from_str_cat = " from " . TABLE_CATEGORIES_DESCRIPTION . " cd, " . TABLE_CATEGORIES . " c ";
$where_str_cat = " where c.categories_status <> 0 AND cd.categories_id = c.categories_id and cd.language_id = '" . (int)$_SESSION['languages_id'] . "'";   //2011-03-24 Included c.categories_status <> 0 AND condition to avoid displaying Disabled categories


if (!isset($_GET['inc_subcat'])) {
  $_GET['inc_subcat'] = '0';
}

if (isset($_GET['categories_id']) && zen_not_null($_GET['categories_id'])) {
  if ($_GET['inc_subcat'] == '1') {
    $subcategories_array = array();
    zen_get_subcategories($subcategories_array, $_GET['categories_id']);
    $where_str_cat .= " AND (cd.categories_id = :categoriesID";

    $where_str_cat = $db->bindVars($where_str_cat, ':categoriesID', $_GET['categories_id'], 'integer');

    for ($i=0, $n=sizeof($subcategories_array); $i<$n; $i++ ) {
      $where_str_cat .= " OR cd.categories_id = :categoriesID";
      $where_str_cat = $db->bindVars($where_str_cat, ':categoriesID', $subcategories_array[$i], 'integer');
    }
    $where_str_cat .= ")";
  } else {
    $where_str_cat .= "  AND (cd.categories_id = :categoriesID";
    $where_str_cat = $db->bindVars($where_str_cat, ':categoriesID', $_GET['categories_id'], 'integer');
    $where_str_cat .= ")";
  }
}

if (isset($keywords) && zen_not_null($keywords)) {
  if (zen_parse_search_string(stripslashes($_GET['keyword']), $search_keywords)) {
    $where_str_cat .= " AND (";
    for ($i=0, $n=sizeof($search_keywords); $i<$n; $i++ ) {
      switch ($search_keywords[$i]) {
        case '(':
        case ')':
        case 'and':
        case 'or':
        $where_str_cat .= " " . $search_keywords[$i] . " ";
        break;
        default:
        $where_str_cat .= "(cd.categories_name LIKE '%:keywords%' OR
                        cd.categories_description LIKE '%:keywords%'";
        $where_str_cat = $db->bindVars($where_str_cat, ':keywords', $search_keywords[$i], 'noquotestring');       
        $where_str_cat .= ')';
        break;
      }
    }
    $where_str_cat .= " )";
  }
}       
                     
/*  For future to include tags and keywords in search.  Not tested, only as guide.  Also, has to be included DB fields in sql and join in from
         $where_str_cat .= " OR (mtpd.metatags_keywords
                        LIKE '%:keywords%'
                        AND mtpd.metatags_keywords !='')";
        $where_str_cat = $db->bindVars($where_str_cat, ':keywords', $search_keywords[$i], 'noquotestring');
        $where_str_cat .= " OR (mtpd.metatags_description
                        LIKE '%:keywords%'
                        AND mtpd.metatags_description !='')";
        $where_str_cat = $db->bindVars($where_str_cat, ':keywords', $search_keywords[$i], 'noquotestring');        
*/  

// Notifier Point
//    $zco_notifier->notify('NOTIFY_SEARCH_WHERE_STRING');

// Sort only by category name 
     $order_str_cat = " order by cd.categories_name";


$listing_sql_cat = $select_str_cat . $from_str_cat . $where_str_cat . $order_str_cat;

$list_of_categories = $db->Execute($listing_sql_cat);

if ($list_of_categories->RecordCount() > 0) {
   $found_categories = true;                         
}
   
?>