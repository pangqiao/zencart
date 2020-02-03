<?php
/**
 * @package FAQ Manager
 * @copyright Copyright 2003-2009 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @Original contrib by Vijay Immanuel for osCommerce, converted to zen by dave@open-operations.com - http://www.open-operations.com
 * @faq_manager_functions.php updated 2012-09-18 to be v1.5 compatible kamelion0927
 */

////
// validate faqs_id
  function zen_faqs_id_valid($valid_id) {
    global $db;
    $check_valid = $db->Execute("select p.faqs_id
                                 from " . TABLE_FAQS . " p
                                 where faqs_id='" . $valid_id . "' limit 1");
    if ($check_valid->EOF) {
      return false;
    } else {
      return true;
    }
  }

////
// Return a faq's category
// TABLES: faqs_to_categories
  function zen_get_faqs_faq_category_id($faqs_id) {
    global $db;

    $the_faqs_faq_category_query = "select faqs_id, faq_categories_id from " . TABLE_FAQS_TO_FAQ_CATEGORIES . " where faqs_id = '" . (int)$faqs_id . "'" . " order by faqs_id, faq_categories_id";
    $the_faqs_faq_category = $db->Execute($the_faqs_faq_category_query);

    return $the_faqs_faq_category->fields['faq_categories_id'];
  }
  
////
// Generate a path to faq_categories
  function zen_get_path_faq($current_faq_category_id = '') {
    global $fcPath_array, $db;
    if (zen_not_null($current_faq_category_id)) {
      $cp_size = sizeof($fcPath_array);
      if ($cp_size == 0) {
        $fcPath_new = $current_faq_category_id;
      } else {
        $fcPath_new = '';
        $last_faq_category_query = "select parent_id
                                from " . TABLE_FAQ_CATEGORIES . "
                                where faq_categories_id = '" . (int)$fcPath_array[($cp_size-1)] . "'";
        $last_faq_category = $db->Execute($last_faq_category_query);
        $current_faq_category_query = "select parent_id
                                   from " . TABLE_FAQ_CATEGORIES . "
                                   where faq_categories_id = '" . (int)$current_faq_category_id . "'";
        $current_faq_category = $db->Execute($current_faq_category_query);
        if ($last_faq_category->fields['parent_id'] == $current_faq_category->fields['parent_id']) {
          for ($i=0; $i<($cp_size-1); $i++) {
            $fcPath_new .= '_' . $fcPath_array[$i];
          }
        } else {
          for ($i=0; $i<$cp_size; $i++) {
            $fcPath_new .= '_' . $fcPath_array[$i];
          }
        }
        $fcPath_new .= '_' . $current_faq_category_id;

        if (substr($fcPath_new, 0, 1) == '_') {
          $fcPath_new = substr($fcPath_new, 1);
        }
      }
    } else {
      $fcPath_new = implode('_', $fcPath_array);
    }
    return 'fcPath=' . $fcPath_new;
  }

////
  function zen_get_faq_categories($faq_categories_array = '', $faq_parent_id = '0', $indent = '', $status_setting = '') {
    global $db;
    if (!is_array($faq_categories_array)) $faq_categories_array = array();
    // show based on status
    if ($status_setting != '') {
      $zc_status = " c.faq_categories_status='" . (int)$status_setting . "' and ";
    } else {
      $zc_status = '';
    }
    $faq_categories_query = "select c.faq_categories_id, cd.faq_categories_name, c.faq_categories_status
                         from " . TABLE_FAQ_CATEGORIES . " c, " . TABLE_FAQ_CATEGORIES_DESCRIPTION . " cd
                         where " . $zc_status . "
                         parent_id = '" . (int)$faq_parent_id . "'
                         and c.faq_categories_id = cd.faq_categories_id
                         and cd.language_id = '" . (int)$_SESSION['languages_id'] . "'
                         order by sort_order, cd.faq_categories_name";
    $faq_categories = $db->Execute($faq_categories_query);
    while (!$faq_categories->EOF) {
      $faq_categories_array[] = array('id' => $faq_categories->fields['faq_categories_id'],
                                  'text' => $indent . $faq_categories->fields['faq_categories_name']);
      if ($faq_categories->fields['faq_categories_id'] != $faq_parent_id) {
        $faq_categories_array = zen_get_faq_categories($faq_categories_array, $faq_categories->fields['faq_categories_id'], $indent . '&nbsp;&nbsp;', '1');
      }
      $faq_categories->MoveNext();
    }
    return $faq_categories_array;
  }

////
// Return all subfaq_category IDs
// TABLES: faq_categories
  function zen_get_subfaq_categories(&$subfaq_categories_array, $faq_parent_id = 0) {
    global $db;
    $subfaq_categories_query = "select faq_categories_id
                            from " . TABLE_FAQ_CATEGORIES . "
                            where parent_id = '" . (int)$faq_parent_id . "'";
    $subfaq_categories = $db->Execute($subfaq_categories_query);
    while (!$subfaq_categories->EOF) {
      $subfaq_categories_array[sizeof($subfaq_categories_array)] = $subfaq_categories->fields['faq_categories_id'];
      if ($subfaq_categories->fields['faq_categories_id'] != $faq_parent_id) {
        zen_get_subfaq_categories($subfaq_categories_array, $subfaq_categories->fields['faq_categories_id']);
      }
      $subfaq_categories->MoveNext();
    }
  }
  
////
// Recursively go through the faq_categories and retreive all parent faq_categories IDs
// TABLES: faq_categories
  function zen_get_parent_faq_categories(&$faq_categories, $faq_categories_id) {
    global $db;
    $parent_faq_categories_query = "select parent_id
                                from " . TABLE_FAQ_CATEGORIES . "
                                where faq_categories_id = '" . (int)$faq_categories_id . "'";
    $parent_faq_categories = $db->Execute($parent_faq_categories_query);
    while (!$parent_faq_categories->EOF) {
      if ($parent_faq_categories->fields['parent_id'] == 0) return true;
      $faq_categories[sizeof($faq_categories)] = $parent_faq_categories->fields['parent_id'];
      if ($parent_faq_categories->fields['parent_id'] != $faq_categories_id) {
        zen_get_parent_faq_categories($faq_categories, $parent_faq_categories->fields['parent_id']);
      }
      $parent_faq_categories->MoveNext();
    }
  }
  
////
// Construct a category path to the product
// TABLES: products_to_categories
  function zen_get_faq_path($faqs_id) {
    global $db;
    $fcPath = '';
    $faq_category_query = "select p2c.faq_categories_id
                       from " . TABLE_FAQS . " p, " . TABLE_FAQS_TO_FAQ_CATEGORIES . " p2c
                       where p.faqs_id = '" . (int)$faqs_id . "'
                       and p.faqs_status = '1'
                       and p.faqs_id = p2c.faqs_id limit 1";
    $faq_category = $db->Execute($faq_category_query);
    if ($faq_category->RecordCount() > 0) {
      $faq_categories = array();
      zen_get_parent_faq_categories($faq_categories, $faq_category->fields['faq_categories_id']);
      $faq_categories = array_reverse($faq_categories);
      $fcPath = implode('_', $faq_categories);
      if (zen_not_null($fcPath)) $fcPath .= '_';
      $fcPath .= $faq_category->fields['faq_categories_id'];
    }
    return $fcPath;
  }
  
////
// Parse and secure the fcPath parameter values
  function zen_parse_faq_category_path($fcPath) {
// make sure the faq_category IDs are integers
    $fcPath_array = array_map('zen_string_to_int', explode('_', $fcPath));
// make sure no duplicate faq_category IDs exist which could lock the server in a loop
    $tmp_array = array();
    $n = sizeof($fcPath_array);
    for ($i=0; $i<$n; $i++) {
      if (!in_array($fcPath_array[$i], $tmp_array)) {
        $tmp_array[] = $fcPath_array[$i];
      }
    }
    return $tmp_array;
  }

  function zen_faq_in_faq_category($faq_id, $cat_id) {
    global $db;
    $in_cat=false;
    $faq_category_query_raw = "select faq_categories_id from " . TABLE_FAQS_TO_FAQ_CATEGORIES . "
                           where faqs_id = '" . (int)$faq_id . "'";
    $faq_category = $db->Execute($faq_category_query_raw);
    while (!$faq_category->EOF) {
      if ($faq_category->fields['faq_categories_id'] == $cat_id) $in_cat = true;
      if (!$in_cat) {
        $parent_faq_categories_query = "select parent_id from " . TABLE_FAQ_CATEGORIES . "
                                    where faq_categories_id = '" . $faq_category->fields['faq_categories_id'] . "'";
        $parent_faq_categories = $db->Execute($parent_faq_categories_query);
        while (!$parent_faq_categories->EOF) {
          if (($parent_faq_categories->fields['parent_id'] !=0) ) {
            if (!$in_cat) $in_cat = zen_faq_in_parent_faq_category($faq_id, $cat_id, $parent_faq_categories->fields['parent_id']);
          }
          $parent_faq_categories->MoveNext();
        }
      }
      $faq_category->MoveNext();
    }
    return $in_cat;
  }

  function zen_faq_in_parent_faq_category($faq_id, $cat_id, $parent_cat_id) {
    global $db;
    if ($cat_id == $parent_cat_id) {
      $in_cat = true;
    } else {
      $parent_faq_categories_query = "select parent_id from " . TABLE_FAQ_CATEGORIES . "
                                  where faq_categories_id = '" . (int)$parent_cat_id . "'";
      $parent_faq_categories = $db->Execute($parent_faq_categories_query);
      while (!$parent_faq_categories->EOF) {
        if ($parent_faq_categories->fields['parent_id'] !=0 && !$incat) {
          $in_cat = zen_faq_in_parent_faq_category($faq_id, $cat_id, $parent_faq_categories->fields['parent_id']);
        }
        $parent_faq_categories->MoveNext();
      }
    }
    return $in_cat;
  }

//// look up parent faq categories name
  function zen_get_faq_categories_name($who_am_i) {
    global $db;
    $the_faq_categories_name_query= "select faq_categories_name from " . TABLE_FAQ_CATEGORIES_DESCRIPTION . " where faq_categories_id= '" . $who_am_i . "' and language_id= '" . $_SESSION['languages_id'] . "'";
    $the_faq_categories_name = $db->Execute($the_faq_categories_name_query);
    return $the_faq_categories_name->fields['faq_categories_name'];
  }
  
  function zen_get_info_faq_page($zf_faq_id) {
    global $db;
      return 'faqs_info';
  }
  
////
// Return the number of faqs in a category
// TABLES: faqs, faqs_to_faq_categories, faq_categories
  function zen_count_faqs_in_faq_category($faq_category_id, $include_inactive = false) {
    global $db;
    $faqs_count = 0;
    if ($include_inactive == true) {
      $faqs_query = "select count(*) as total
                         from " . TABLE_FAQS . " p, " . TABLE_FAQS_TO_FAQ_CATEGORIES . " p2c
                         where p.faqs_id = p2c.faqs_id
                         and p2c.faq_categories_id = '" . (int)$faq_category_id . "'";
    } else {
      $faqs_query = "select count(*) as total
                         from " . TABLE_FAQS . " p, " . TABLE_FAQS_TO_FAQ_CATEGORIES . " p2c
                         where p.faqs_id = p2c.faqs_id
                         and p.faqs_status = '1'
                         and p2c.faq_categories_id = '" . (int)$faq_category_id . "'";
    }
    $faqs = $db->Execute($faqs_query);
    $faqs_count += $faqs->fields['total'];
    $child_faq_categories_query = "select faq_categories_id
                               from " . TABLE_FAQ_CATEGORIES . "
                               where parent_id = '" . (int)$faq_category_id . "'";
    $child_faq_categories = $db->Execute($child_faq_categories_query);
    if ($child_faq_categories->RecordCount() > 0) {
      while (!$child_faq_categories->EOF) {
        $faqs_count += zen_count_faqs_in_faq_category($child_faq_categories->fields['faq_categories_id'], $include_inactive);
        $child_faq_categories->MoveNext();
      }
    }
    return $faqs_count;
  }
?>