<?php
/**
 * @package FAQ Manager
 * @copyright Copyright 2003-2011 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * based on @version $Id: general.php 19330 2011-08-07 06:32:56Z drbyte $
 * @Original contrib by Vijay Immanuel for osCommerce, converted to zen by dave@open-operations.com - http://www.open-operations.com
 * @faq_manager_functions.php updated 2012-09-18 to be v1.5 compatible kamelion0927
 */

// calculate faq_category path
  if (isset($_GET['fcPath'])) {
    $fcPath = $_GET['fcPath'];
  } else {
    $fcPath = '';
  }
  if (zen_not_null($fcPath)) {
    $fcPath_array = zen_parse_faq_category_path($fcPath);
    $fcPath = implode('_', $fcPath_array);
    $current_faq_category_id = $fcPath_array[(sizeof($fcPath_array)-1)];
  } else {
    $current_faq_category_id = 0;
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

  function zen_get_faq_category_tree($faq_parent_id = '0', $spacing = '', $exclude = '', $faq_category_tree_array = '', $include_itself = false, $faq_category_has_faqs = false, $limit = false) {
    global $db;
    if ($limit) {
      $limit_count = " limit 1";
    } else {
      $limit_count = '';
    }

    if (!is_array($faq_category_tree_array)) $faq_category_tree_array = array();
    if ((sizeof($faq_category_tree_array) < 1) && ($exclude != '0')) $faq_category_tree_array[] = array('id' => '0', 'text' => TEXT_TOP);

    if ($include_itself) {
      $faq_category = $db->Execute("select cd.faq_categories_name
                                from " . TABLE_FAQ_CATEGORIES_DESCRIPTION . " cd
                                where cd.language_id = '" . (int)$_SESSION['languages_id'] . "'
                                and cd.faq_categories_id = '" . (int)$faq_parent_id . "'");

      $faq_category_tree_array[] = array('id' => $faq_parent_id, 'text' => $faq_category->fields['faq_categories_name']);
    }

    $faq_categories = $db->Execute("select c.faq_categories_id, cd.faq_categories_name, c.parent_id
                                from " . TABLE_FAQ_CATEGORIES . " c, " . TABLE_FAQ_CATEGORIES_DESCRIPTION . " cd
                                where c.faq_categories_id = cd.faq_categories_id
                                and cd.language_id = '" . (int)$_SESSION['languages_id'] . "'
                                and c.parent_id = '" . (int)$faq_parent_id . "'
                                order by c.sort_order, cd.faq_categories_name");

    while (!$faq_categories->EOF) {
      if ($faq_category_has_faqs == true and zen_faqs_in_faq_category_count($faq_categories->fields['faq_categories_id'], '', false, true) >= 1) {
        $mark = '*';
      } else {
        $mark = '&nbsp;&nbsp;';
      }
      if ($exclude != $faq_categories->fields['faq_categories_id']) $faq_category_tree_array[] = array('id' => $faq_categories->fields['faq_categories_id'], 'text' => $spacing . $faq_categories->fields['faq_categories_name'] . $mark);
      $faq_category_tree_array = zen_get_faq_category_tree($faq_categories->fields['faq_categories_id'], $spacing . '&nbsp;&nbsp;&nbsp;', $exclude, $faq_category_tree_array, '', $faq_category_has_faqs);
      $faq_categories->MoveNext();
    }
    return $faq_category_tree_array;
  }

  function zen_get_faq_category_name($faq_category_id, $language_id) {
    global $db;
    $faq_category = $db->Execute("select faq_categories_name
                              from " . TABLE_FAQ_CATEGORIES_DESCRIPTION . "
                              where faq_categories_id = '" . (int)$faq_category_id . "'
                              and language_id = '" . (int)$language_id . "'");

    return $faq_category->fields['faq_categories_name'];
  }


  function zen_get_faq_category_description($faq_category_id, $language_id) {
    global $db;
    $faq_category = $db->Execute("select faq_categories_description
                              from " . TABLE_FAQ_CATEGORIES_DESCRIPTION . "
                              where faq_categories_id = '" . (int)$faq_category_id . "'
                              and language_id = '" . (int)$language_id . "'");

    return $faq_category->fields['faq_categories_description'];
  }

  function zen_get_faqs_name($faq_id, $language_id = 0) {
    global $db;

    if ($language_id == 0) $language_id = $_SESSION['languages_id'];
    $faq = $db->Execute("select faqs_name
                             from " . TABLE_FAQS_DESCRIPTION . "
                             where faqs_id = '" . (int)$faq_id . "'
                             and language_id = '" . (int)$language_id . "'");

    return $faq->fields['faqs_name'];
  }
  
  function zen_get_faqs_answer($faq_id, $language_id) {
    global $db;
    $faq = $db->Execute("select faqs_answer
                             from " . TABLE_FAQS_DESCRIPTION . "
                             where faqs_id = '" . (int)$faq_id . "'
                             and language_id = '" . (int)$language_id . "'");

    return $faq->fields['faqs_answer'];
  }

////
// Count how many faqs exist in a faq_category
// TABLES: faqs, faqs_to_faq_categories, faq_categories
  function zen_faqs_in_faq_category_count($faq_categories_id, $include_deactivated = false, $include_child = true, $limit = false) {
    global $db;
    $faqs_count = 0;

    if ($limit) {
      $limit_count = ' limit 1';
    } else {
      $limit_count = '';
    }

    if ($include_deactivated) {

      $faqs = $db->Execute("select count(*) as total
                                from " . TABLE_FAQS . " p, " . TABLE_FAQS_TO_FAQ_CATEGORIES . " p2c
                                where p.faqs_id = p2c.faqs_id
                                and p2c.faq_categories_id = '" . (int)$faq_categories_id . "'" . $limit_count);
    } else {
      $faqs = $db->Execute("select count(*) as total
                                from " . TABLE_FAQS . " p, " . TABLE_FAQS_TO_FAQ_CATEGORIES . " p2c
                                where p.faqs_id = p2c.faqs_id
                                and p.faqs_status = 1
                                and p2c.faq_categories_id = '" . (int)$faq_categories_id . "'" . $limit_count);

    }

    $faqs_count += $faqs->fields['total'];

    if ($include_child) {
      $childs = $db->Execute("select faq_categories_id from " . TABLE_FAQ_CATEGORIES . "
                              where parent_id = '" . (int)$faq_categories_id . "'");
      if ($childs->RecordCount() > 0 ) {
        while (!$childs->EOF) {
          $faqs_count += zen_faqs_in_faq_category_count($childs->fields['faq_categories_id'], $include_deactivated);
          $childs->MoveNext();
        }
      }
    }
    return $faqs_count;
  }

////
// Sets the status of a faq 
  function zen_set_faq_status($faqs_id, $status) {
    global $db;
    if ($status == '1') {
      return $db->Execute("update " . TABLE_FAQS . "
                           set faqs_status = 1, faqs_last_modified = now()
                           where faqs_id = '" . (int)$faqs_id . "'");
    } elseif ($status == '0') {
      return $db->Execute("update " . TABLE_FAQS . "
                           set faqs_status = 0, faqs_last_modified = now()
                           where faqs_id = '" . (int)$faqs_id . "'");
    } else {
      return -1;
    }
  }

  function zen_generate_faq_category_path($id, $from = 'faq_category', $faq_categories_array = '', $index = 0) {
    global $db;

    if (!is_array($faq_categories_array)) $faq_categories_array = array();

    if ($from == 'faq') {
      $faq_categories = $db->Execute("select faq_categories_id
                                  from " . TABLE_FAQS_TO_FAQ_CATEGORIES . "
                                  where faqs_id = '" . (int)$id . "'");

      while (!$faq_categories->EOF) {
        if ($faq_categories->fields['faq_categories_id'] == '0') {
          $faq_categories_array[$index][] = array('id' => '0', 'text' => TEXT_TOP);
        } else {
          $faq_category = $db->Execute("select cd.faq_categories_name, c.parent_id
                                    from " . TABLE_FAQ_CATEGORIES . " c, " . TABLE_FAQ_CATEGORIES_DESCRIPTION . " cd
                                    where c.faq_categories_id = '" . (int)$faq_categories->fields['faq_categories_id'] . "'
                                    and c.faq_categories_id = cd.faq_categories_id
                                    and cd.language_id = '" . (int)$_SESSION['languages_id'] . "'");

          $faq_categories_array[$index][] = array('id' => $faq_categories->fields['faq_categories_id'], 'text' => $faq_category->fields['faq_categories_name']);
          if ( (zen_not_null($faq_category->fields['parent_id'])) && ($faq_category->fields['parent_id'] != '0') ) $faq_categories_array = zen_generate_faq_category_path($faq_category->fields['parent_id'], 'faq_category', $faq_categories_array, $index);
          $faq_categories_array[$index] = array_reverse($faq_categories_array[$index]);
        }
        $index++;
        $faq_categories->MoveNext();
      }
    } elseif ($from == 'faq_category') {
      $faq_category = $db->Execute("select cd.faq_categories_name, c.parent_id
                                from " . TABLE_FAQ_CATEGORIES . " c, " . TABLE_FAQ_CATEGORIES_DESCRIPTION . " cd
                                where c.faq_categories_id = '" . (int)$id . "'
                                and c.faq_categories_id = cd.faq_categories_id
                                and cd.language_id = '" . (int)$_SESSION['languages_id'] . "'");
      if (!$faq_category->EOF) {
      $faq_categories_array[$index][] = array('id' => $id, 'text' => $faq_category->fields['faq_categories_name']);
      if ( (zen_not_null($faq_category->fields['parent_id'])) && ($faq_category->fields['parent_id'] != '0') ) $faq_categories_array = zen_generate_faq_category_path($faq_category->fields['parent_id'], 'faq_category', $faq_categories_array, $index);
      }
    }

    return $faq_categories_array;
  }

  function zen_output_generated_faq_category_path($id, $from = 'faq_category') {
    $calculated_faq_category_path_string = '';
    $calculated_faq_category_path = zen_generate_faq_category_path($id, $from);
    for ($i=0, $n=sizeof($calculated_faq_category_path); $i<$n; $i++) {
      for ($j=0, $k=sizeof($calculated_faq_category_path[$i]); $j<$k; $j++) {
        $calculated_faq_category_path_string = $calculated_faq_category_path[$i][$j]['text'] . '&nbsp;&gt;&nbsp;' . $calculated_faq_category_path_string;
      }
      $calculated_faq_category_path_string = substr($calculated_faq_category_path_string, 0, -16) . '<br>';
    }
    $calculated_faq_category_path_string = substr($calculated_faq_category_path_string, 0, -4);
    if (strlen($calculated_faq_category_path_string) < 1) $calculated_faq_category_path_string = TEXT_TOP;
    return $calculated_faq_category_path_string;
  }

  function zen_get_generated_faq_category_path_ids($id, $from = 'faq_category') {
    global $db;
    $calculated_faq_category_path_string = '';
    $calculated_faq_category_path = zen_generate_faq_category_path($id, $from);
    for ($i=0, $n=sizeof($calculated_faq_category_path); $i<$n; $i++) {
      for ($j=0, $k=sizeof($calculated_faq_category_path[$i]); $j<$k; $j++) {
        $calculated_faq_category_path_string .= $calculated_faq_category_path[$i][$j]['id'] . '_';
      }
      $calculated_faq_category_path_string = substr($calculated_faq_category_path_string, 0, -1) . '<br>';
    }
    $calculated_faq_category_path_string = substr($calculated_faq_category_path_string, 0, -4);
    if (strlen($calculated_faq_category_path_string) < 1) $calculated_faq_category_path_string = TEXT_TOP;
    return $calculated_faq_category_path_string;
  }

  function zen_remove_faq_category($faq_category_id) {
    if ((int)$faq_category_id == 0) return;
    global $db;
    $db->Execute("delete from " . TABLE_FAQ_CATEGORIES . "
                  where faq_categories_id = '" . (int)$faq_category_id . "'");
    $db->Execute("delete from " . TABLE_FAQ_CATEGORIES_DESCRIPTION . "
                  where faq_categories_id = '" . (int)$faq_category_id . "'");
    $db->Execute("delete from " . TABLE_FAQS_TO_FAQ_CATEGORIES . "
                  where faq_categories_id = '" . (int)$faq_category_id . "'");
  }

  function zen_remove_faq($faq_id, $ptc = 'true') {
    global $db;
    $db->Execute("delete from " . TABLE_FAQS . "
                  where faqs_id = '" . (int)$faq_id . "'");

    $db->Execute("delete from " . TABLE_FAQS_TO_FAQ_CATEGORIES . "
                    where faqs_id = '" . (int)$faq_id . "'");

    $db->Execute("delete from " . TABLE_FAQS_DESCRIPTION . "
                  where faqs_id = '" . (int)$faq_id . "'");
  }
  
////
// Check if faq_id is valid
  function zen_faqs_id_valid($faqs_id) {
    global $db;
    $faqs_valid_query = "select count(*) as count
                         from " . TABLE_FAQS . "
                         where faqs_id = '" . (int)$faqs_id . "'";
    $faqs_valid = $db->Execute($faqs_valid_query);
    if ($faqs_valid->fields['count'] > 0) {
      return true;
    } else {
      return false;
    }
  }
  
////
// Construct a faq_category path to the faq
// TABLES: faqs_to_faq_categories
  function zen_get_faq_path($faqs_id, $status_override = '1') {
    global $db;
    $fcPath = '';

    $faq_category_query = "select p2c.faq_categories_id
                       from " . TABLE_FAQS . " p, " . TABLE_FAQS_TO_FAQ_CATEGORIES . " p2c
                       where p.faqs_id = '" . (int)$faqs_id . "' " .
                       ($status_override == '1' ? " and p.faqs_status = '1' " : '') . "
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
// Return a faq's faq_category
// TABLES: faqs_to_faq_categories
  function zen_get_faqs_faq_category_id($faqs_id) {
    global $db;
    $the_faqs_faq_category_query = "select faqs_id, faq_categories_id from " . TABLE_FAQS_TO_FAQ_CATEGORIES . " where faqs_id = '" . $faqs_id . "'" . " order by faqs_id,faq_categories_id";
    $the_faqs_faq_category = $db->Execute($the_faqs_faq_category_query);
    return $the_faqs_faq_category->fields['faq_categories_id'];
  }

////
// Return true if the faq_category has subfaq_categories
// TABLES: faq_categories
  function zen_has_faq_category_subfaq_categories($faq_category_id) {
    global $db;
    $child_faq_category_query = "select count(*) as count
                             from " . TABLE_FAQ_CATEGORIES . "
                             where parent_id = '" . (int)$faq_category_id . "'";
    $child_faq_category = $db->Execute($child_faq_category_query);

    if ($child_faq_category->fields['count'] > 0) {
      return true;
    } else {
      return false;
    }
  }

////
  function zen_get_faq_categories($faq_categories_array = '', $faq_parent_id = '0', $indent = '') {
    global $db;

    if (!is_array($faq_categories_array)) $faq_categories_array = array();

    $faq_categories_query = "select c.faq_categories_id, cd.faq_categories_name
                         from " . TABLE_FAQ_CATEGORIES . " c, " . TABLE_FAQ_CATEGORIES_DESCRIPTION . " cd
                         where parent_id = '" . (int)$parent_id . "'
                         and c.faq_categories_id = cd.faq_categories_id
                         and cd.language_id = '" . (int)$_SESSION['languages_id'] . "'
                         order by sort_order, cd.faq_categories_name";

    $faq_categories = $db->Execute($faq_categories_query);

    while (!$faq_categories->EOF) {
      $faq_categories_array[] = array('id' => $faq_categories->fields['faq_categories_id'],
                                  'text' => $indent . $faq_categories->fields['faq_categories_name']);

      if ($faq_categories->fields['faq_categories_id'] != $parent_id) {
        $faq_categories_array = zen_get_faq_categories($faq_categories_array, $faq_categories->fields['faq_categories_id'], $indent . '&nbsp;&nbsp;');
      }
      $faq_categories->MoveNext();
    }

    return $faq_categories_array;
  }


////
// Get the status of a faq_category
  function zen_get_faq_categories_status($faq_categories_id) {
    global $db;
    $sql = "select faq_categories_status from " . TABLE_FAQ_CATEGORIES . (zen_not_null($faq_categories_id) ? " where faq_categories_id=" . (int)$faq_categories_id : "");
    $check_status = $db->Execute($sql);
    return $check_status->fields['faq_categories_status'];
  }

////
// Get the status of a faq
  function zen_get_faqs_status($faq_id) {
    global $db;
    $sql = "select faqs_status from " . TABLE_FAQS . (zen_not_null($faq_id) ? " where faqs_id=" . (int)$faq_id : "");
    $check_status = $db->Execute($sql);
    return $check_status->fields['faqs_status'];
  }

////
// check if linked
  function zen_get_faq_is_linked($faq_id, $show_count = 'false') {
    global $db;
    $sql = "select * from " . TABLE_FAQS_TO_FAQ_CATEGORIES . (zen_not_null($faq_id) ? " where faqs_id=" . (int)$faq_id : "");
    $check_linked = $db->Execute($sql);
    if ($check_linked->RecordCount() > 1) {
      if ($show_count == 'true') {
        return $check_linked->RecordCount();
      } else {
        return 'true';
      }
    } else {
      return 'false';
    }
  }

////
// TABLES: faq_categories_name from faqs_id
  function zen_get_faq_categories_name_from_faq($faq_id) {
    global $db;

    $check_faqs_faq_category = $db->Execute("select faqs_id, faq_categories_id from " . TABLE_FAQS_TO_FAQ_CATEGORIES . " where faqs_id = '" . (int)$faq_id . "'");
    $the_faq_categories_name= $db->Execute("select faq_categories_name from " . TABLE_FAQ_CATEGORIES_DESCRIPTION . " where faq_categories_id= '" . $check_faqs_faq_category->fields['faq_categories_id'] . "' and language_id= '" . (int)$_SESSION['languages_id'] . "'");
    return $the_faq_categories_name->fields['faq_categories_name'];
  }

  function zen_count_faqs_in_cats($faq_category_id) {
    global $db;
    $cat_faqs_query = "select count(if (p.faqs_status=1,1,NULL)) as pr_on, count(*) as total
                           from " . TABLE_FAQS . " p, " . TABLE_FAQS_TO_FAQ_CATEGORIES . " p2c
                           where p.faqs_id = p2c.faqs_id
                           and p2c.faq_categories_id = '" . (int)$faq_category_id . "'";

    $pr_count = $db->Execute($cat_faqs_query);
    $c_array['this_count'] += $pr_count->fields['total'];
    $c_array['this_count_on'] += $pr_count->fields['pr_on'];

    $cat_child_faq_categories_query = "select faq_categories_id
                               from " . TABLE_FAQ_CATEGORIES . "
                               where parent_id = '" . (int)$faq_category_id . "'";

    $cat_child_faq_categories = $db->Execute($cat_child_faq_categories_query);

    if ($cat_child_faq_categories->RecordCount() > 0) {
      while (!$cat_child_faq_categories->EOF) {
          $m_array = zen_count_faqs_in_cats($cat_child_faq_categories->fields['faq_categories_id']);
          $c_array['this_count'] += $m_array['this_count'];
          $c_array['this_count_on'] += $m_array['this_count_on'];
        $cat_child_faq_categories->MoveNext();
      }
    }
    return $c_array;
 }

////
// Return the number of faqs in a faq_category
// TABLES: faqs, faqs_to_faq_categories, faq_categories
// syntax for count: zen_get_faqs_to_faq_categories($faq_categories->fields['faq_categories_id'], true)
// syntax for linked faqs: zen_get_faqs_to_faq_categories($faq_categories->fields['faq_categories_id'], true, 'faqs_active')
  function zen_get_faqs_to_faq_categories($faq_category_id, $include_inactive = false, $counts_what = 'faqs') {
    global $db;

    $faqs_count = 0;
    if ($include_inactive == true) {
      switch ($counts_what) {
        case ('faqs'):
        $cat_faqs_query = "select count(*) as total
                           from " . TABLE_FAQS . " p, " . TABLE_FAQS_TO_FAQ_CATEGORIES . " p2c
                           where p.faqs_id = p2c.faqs_id
                           and p2c.faq_categories_id = '" . (int)$faq_category_id . "'";
        break;
        case ('faqs_active'):
        $cat_faqs_query = "select p.faqs_id
                           from " . TABLE_FAQS . " p, " . TABLE_FAQS_TO_FAQ_CATEGORIES . " p2c
                           where p.faqs_id = p2c.faqs_id
                           and p2c.faq_categories_id = '" . (int)$faq_category_id . "'";
        break;
      }

    } else {
      switch ($counts_what) {
        case ('faqs'):
          $cat_faqs_query = "select count(*) as total
                             from " . TABLE_FAQS . " p, " . TABLE_FAQS_TO_FAQ_CATEGORIES . " p2c
                             where p.faqs_id = p2c.faqs_id
                             and p.faqs_status = 1
                             and p2c.faq_categories_id = '" . (int)$faq_category_id . "'";
        break;
        case ('faqs_active'):
          $cat_faqs_query = "select p.faqs_id
                             from " . TABLE_FAQS . " p, " . TABLE_FAQS_TO_FAQ_CATEGORIES . " p2c
                             where p.faqs_id = p2c.faqs_id
                             and p.faqs_status = 1
                             and p2c.faq_categories_id = '" . (int)$faq_category_id . "'";
        break;
      }

    }
    $cat_faqs = $db->Execute($cat_faqs_query);
      switch ($counts_what) {
        case ('faqs'):
          $cat_faqs_count += $cat_faqs->fields['total'];
          break;
        case ('faqs_active'):
        while (!$cat_faqs->EOF) {
          if (zen_get_faq_is_linked($cat_faqs->fields['faqs_id']) == 'true') {
            return $faqs_linked = 'true';
          }
          $cat_faqs->MoveNext();
        }
          break;
      }
    $cat_child_faq_categories_query = "select faq_categories_id
                               from " . TABLE_FAQ_CATEGORIES . "
                               where parent_id = '" . (int)$faq_category_id . "'";

    $cat_child_faq_categories = $db->Execute($cat_child_faq_categories_query);

    if ($cat_child_faq_categories->RecordCount() > 0) {
      while (!$cat_child_faq_categories->EOF) {
      switch ($counts_what) {
        case ('faqs'):
          $cat_faqs_count += zen_get_faqs_to_faq_categories($cat_child_faq_categories->fields['faq_categories_id'], $include_inactive);
          break;
        case ('faqs_active'):
          if (zen_get_faqs_to_faq_categories($cat_child_faq_categories->fields['faq_categories_id'], true, 'faqs_active') == 'true') {
            return $faqs_linked = 'true';
          }
          break;
        }
        $cat_child_faq_categories->MoveNext();
      }
    }
      switch ($counts_what) {
        case ('faqs'):
          return $cat_faqs_count;
          break;
        case ('faqs_active'):
          return $faqs_linked;
          break;
      }
  }

////
// master faq_category selection
  function zen_get_master_faq_categories_pulldown($faq_id) {
    global $db;

    $master_faq_category_array = array();
    $master_faq_categories_query = $db->Execute("select ptc.faqs_id, cd.faq_categories_name, cd.faq_categories_id
                                    from " . TABLE_FAQS_TO_FAQ_CATEGORIES . " ptc
                                    left join " . TABLE_FAQ_CATEGORIES_DESCRIPTION . " cd
                                    on cd.faq_categories_id = ptc.faq_categories_id
                                    where ptc.faqs_id='" . (int)$faq_id . "'
                                    and cd.language_id = '" . (int)$_SESSION['languages_id'] . "'
                                    ");

    while (!$master_faq_categories_query->EOF) {
      $master_faq_category_array[] = array('id' => $master_faq_categories_query->fields['faq_categories_id'], 'text' => $master_faq_categories_query->fields['faq_categories_name'] . TEXT_INFO_ID . $master_faq_categories_query->fields['faq_categories_id']);
      $master_faq_categories_query->MoveNext();
    }
    return $master_faq_category_array;
  }

////
// TABLES: categories
  function zen_get_parent_faq_category_id($faq_id) {
    global $db;
    $faq_categories_lookup = $db->Execute("select master_faq_categories_id
                                from " . TABLE_FAQS . "
                                where faqs_id = '" . (int)$faq_id . "'");

    $parent_id = $faqs_categories_lookup->fields['master_faq_categories_id'];
    return $parent_id;
  }
  
////
// return any field from faqs or faqs_description table
// Example: zen_faqs_lookup('3', 'faqs_date_added');
  function zen_faqs_lookup($faq_id, $what_field = 'faqs_name', $language = '') {
    global $db;
    if (empty($language)) $language = $_SESSION['languages_id'];
    $faq_lookup = $db->Execute("select " . zen_db_input($what_field) . " as lookup_field
                              from " . TABLE_FAQS . " p, " . TABLE_FAQS_DESCRIPTION . " pd
                              where  p.faqs_id ='" . (int)$faq_id . "'
                              and pd.faqs_id = p.faqs_id
                              and pd.language_id = '" . (int)$language . "'");
    $return_field = $faq_lookup->fields['lookup_field'];
    return $return_field;
  }
  
////
// Get all faqs_id in a FAQ Category and its SubCategories
  function zen_get_faq_categories_faqs_list($faq_categories_id, $include_deactivated = false, $include_child = true) {
    global $db;
    global $faq_categories_faqs_id_list;
    if ($include_deactivated) {
      $faqs = $db->Execute("select p.faqs_id
                                from " . TABLE_FAQS . " p, " . TABLE_FAQS_TO_FAQ_CATEGORIES . " p2c
                                where p.faqs_id = p2c.faqs_id
                                and p2c.faq_categories_id = '" . (int)$faq_categories_id . "'");
    } else {
      $faqs = $db->Execute("select p.faqs_id
                                from " . TABLE_FAQS . " p, " . TABLE_FAQS_TO_FAQ_CATEGORIES . " p2c
                                where p.faqs_id = p2c.faqs_id
                                and p.faqs_status = '1'
                                and p2c.faq_categories_id = '" . (int)$faq_categories_id . "'");
    }

    while (!$faqs->EOF) {
// faq_categories_faqs_id_list keeps resetting when faq category changes ...
      $faq_categories_faqs_id_list[] = $faqs->fields['faqs_id'];
      $faqs->MoveNext();
    }

    if ($include_child) {
      $childs = $db->Execute("select faq_categories_id from " . TABLE_FAQ_CATEGORIES . "
                              where parent_id = '" . (int)$faq_categories_id . "'");
      if ($childs->RecordCount() > 0 ) {
        while (!$childs->EOF) {
          zen_get_faq_categories_faqs_list($childs->fields['faq_categories_id'], $include_deactivated);
          $childs->MoveNext();
        }
      }
    }
    $faqs_id_listing = $faq_categories_faqs_id_list;
    return $faqs_id_listing;
  }
  
    function zen_get_faq_category_path($current_faq_category_id = '') {
    global $fcPath_array, $db;
// set to 0 if Top Level
    if ($current_faq_category_id == '') {
      if (empty($fcPath_array)) {
        $fcPath_new= '';
      } else {
        $fcPath_new = implode('_', $fcPath_array);
      }
    } else {
      if (sizeof($fcPath_array) == 0) {
        $fcPath_new = $current_faq_category_id;
      } else {
        $fcPath_new = '';
        $last_faq_category = $db->Execute("select parent_id
                                       from " . TABLE_FAQ_CATEGORIES . "
                                       where faq_categories_id = '" . (int)$fcPath_array[(sizeof($fcPath_array)-1)] . "'");

        $current_faq_category = $db->Execute("select parent_id
                                          from " . TABLE_FAQ_CATEGORIES . "
                                           where faq_categories_id = '" . (int)$current_faq_category_id . "'");

        if ($last_faq_category->fields['parent_id'] == $current_faq_category->fields['parent_id']) {
          for ($i = 0, $n = sizeof($fcPath_array) - 1; $i < $n; $i++) {
            $fcPath_new .= '_' . $fcPath_array[$i];
          }
        } else {
          for ($i = 0, $n = sizeof($fcPath_array); $i < $n; $i++) {
            $fcPath_new .= '_' . $fcPath_array[$i];
          }
        }
        $fcPath_new .= '_' . $current_faq_category_id;
        if (substr($fcPath_new, 0, 1) == '_') {
          $fcPath_new = substr($fcPath_new, 1);
        }
      }
    }
    return 'fcPath=' . $fcPath_new;
  }
  
  function zen_set_featured_faq_status($featured_faqs_id, $status) {
    global $db;
    $sql = "update " . TABLE_FAQS_FEATURED . "
            set status = '" . (int)$status . "', date_status_change = now()
            where featured_faqs_id = '" . (int)$featured_faqs_id . "'";
    return $db->Execute($sql);
   }
   
   function zen_expire_featured_faq() {
    global $db;
    $date_range = time();
    $zc_featured_date = date('Ymd', $date_range);
    $featured_query = "select featured_faqs_id
                       from " . TABLE_FAQS_FEATURED . "
                       where status = '1'
                       and ((" . $zc_featured_date . " >= expires_date and expires_date != '0001-01-01')
                       or (" . $zc_featured_date . " < featured_date_available and featured_date_available != '0001-01-01'))";
    $featured = $db->Execute($featured_query);
    if ($featured->RecordCount() > 0) {
      while (!$featured->EOF) {
        zen_set_featured_faq_status($featured->fields['featured_faqs_id'], '0');
        $featured->MoveNext();
      }
    }
  }
  
  function zen_start_featured_faq() {
    global $db;
    $date_range = time();
    $zc_featured_date = date('Ymd', $date_range);
    $featured_faq = "select featured_faqs_id
                       from " . TABLE_FAQS_FEATURED . "
                       where status = '0'
                       and (((featured_faq_date_available <= " . $zc_featured_date . " and featured_faq_date_available != '0001-01-01') and (expires_date > " . $zc_featured_date . "))
                       or ((featured_faq_date_available <= " . $zc_featured_date . " and featured_faq_date_available != '0001-01-01') and (expires_date = '0001-01-01'))
                       or (featured_faq_date_available = '0001-01-01' and expires_date > " . $zc_featured_date . "))
                       ";
    $featured = $db->Execute($featured_query);
    if ($featured->RecordCount() > 0) {
      while (!$featured->EOF) {
        zen_set_featured_faq_status($featured->fields['featured_faqs_id'], '1');
        $featured->MoveNext();
      }
    }
// turn off featured if not active yet
    $featured_query = "select featured_faqs_id
                       from " . TABLE_FAQS_FEATURED . "
                       where status = '1'
                       and (" . $zc_featured_date . " < featured_faq_date_available and featured_faq_date_available != '0001-01-01')
                       ";
    $featured = $db->Execute($featured_query);
    if ($featured->RecordCount() > 0) {
      while (!$featured->EOF) {
        zen_set_featured_faq_status($featured->fields['featured_faqs_id'], '0');
        $featured->MoveNext();
      }
    }
  }
  
////
// faqs with name pulldown
  function zen_draw_faqs_pull_down($name, $parameters = '', $exclude = '', $show_id = false, $set_selected = false, $show_model = false, $show_current_category = false) {
    global $db, $current_faq_category_id;

    if ($exclude == '') {
      $exclude = array();
    }

    $select_string = '<select name="' . $name . '"';

    if ($parameters) {
      $select_string .= ' ' . $parameters;
    }

    $select_string .= '>';

    if ($show_current_faq_category) {
// only show $current_categories_id
      $faqs = $db->Execute("select p.faqs_id, pd.faqs_name, ptc.categories_id
                                from " . TABLE_FAQS . " p
                                left join " . TABLE_FAQS_TO_FAQ_CATEGORIES . " ptc on ptc.faqs_id = p.faqs_id, " .
                                TABLE_FAQS_DESCRIPTION . " pd
                                where p.faqs_id = pd.faqs_id
                                and pd.language_id = '" . (int)$_SESSION['languages_id'] . "'
                                and ptc.categories_id = '" . (int)$current_category_id . "'
                                order by faqs_name");
    } else {
      $faqs = $db->Execute("select p.faqs_id, pd.faqs_name
                                from " . TABLE_FAQS . " p, " . TABLE_FAQS_DESCRIPTION . " pd
                                where p.faqs_id = pd.faqs_id
                                and pd.language_id = '" . (int)$_SESSION['languages_id'] . "'
                                order by faqs_name");
    }

    while (!$faqs->EOF) {
      if (!in_array($faqs->fields['faqs_id'], $exclude)) {
        $select_string .= '<option value="' . $faqs->fields['faqs_id'] . '"';
        if ($set_selected == $faqs->fields['faqs_id']) $select_string .= ' SELECTED';
        $select_string .= '>' . $faqs->fields['faqs_name'] . ($show_id ? ' - ID# ' . $faqs->fields['faqs_id'] : '') . '</option>';
      }
      $faqs->MoveNext();
    }
    $select_string .= '</select>';
    return $select_string;
  }
?>