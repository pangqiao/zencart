<?php
/**
 * @package FAQ Manager
 * @copyright Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * based on @version $Id: category_tree.php 3041 2006-02-15 21:56:45Z wilt $
 * @Original contrib by Vijay Immanuel for osCommerce, converted to zen by dave@open-operations.com - http://www.open-operations.com
 * @faq_category_tree.php updated 2012-10-12 to be v1.5 compatible kamelion0927
 */
 
if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}
  class faq_category_tree  {
    function zen_faq_category_tree($faq_type = "all") {
      global $db, $fcPath, $fcPath_array;
      $this->tree = array();
        $faq_categories_query = "select c.faq_categories_id, cd.faq_categories_name, c.parent_id
                             from " . TABLE_FAQ_CATEGORIES . " c, " . TABLE_FAQ_CATEGORIES_DESCRIPTION . " cd
                             where c.parent_id = 0
                             and c.faq_categories_id = cd.faq_categories_id
                             and cd.language_id='" . (int)$_SESSION['languages_id'] . "'
                             and c.faq_categories_status= 1
                             order by sort_order, cd.faq_categories_name";
      $faq_categories = $db->Execute($faq_categories_query, '', true, 150);
      while (!$faq_categories->EOF)  {
      $this->tree[$faq_categories->fields['faq_categories_id']] = array('name' => $faq_categories->fields['faq_categories_name'],
      'parent' => $faq_categories->fields['parent_id'],
      'level' => 0,
      'path' => $faq_categories->fields['faq_categories_id'],
      'next_id' => false);

      if (isset($faq_parent_id)) {
        $this->tree[$faq_parent_id]['next_id'] = $faq_categories->fields['faq_categories_id'];
      }
      $faq_parent_id = $faq_categories->fields['faq_categories_id'];
      if (!isset($first_element)) {
        $first_element = $faq_categories->fields['faq_categories_id'];
      }
      $faq_categories->MoveNext();
    }
    if (zen_not_null($fcPath)) {
      $new_path = '';
      reset($fcPath_array);
      while (list($key, $value) = each($fcPath_array)) {
        unset($faq_parent_id);
        unset($first_id);
          $faq_categories_query = "select c.faq_categories_id, cd.faq_categories_name, c.parent_id
                               from " . TABLE_FAQ_CATEGORIES . " c, " . TABLE_FAQ_CATEGORIES_DESCRIPTION . " cd
                               where c.parent_id = " . (int)$value . "
                               and c.faq_categories_id = cd.faq_categories_id
                               and cd.language_id=" . (int)$_SESSION['languages_id'] . "
                               and c.faq_categories_status= 1
                               order by sort_order, cd.faq_categories_name";
        $rows = $db->Execute($faq_categories_query);
        if ($rows->RecordCount()>0) {
          $new_path .= $value;
          while (!$rows->EOF) {
            $this->tree[$rows->fields['faq_categories_id']] = array('name' => $rows->fields['faq_categories_name'],
            'parent' => $rows->fields['parent_id'],
            'level' => $key+1,
            'path' => $new_path . '_' . $rows->fields['faq_categories_id'],
            'next_id' => false);
            if (isset($faq_parent_id)) {
              $this->tree[$faq_parent_id]['next_id'] = $rows->fields['faq_categories_id'];
            }
            $faq_parent_id = $rows->fields['faq_categories_id'];
            if (!isset($first_id)) {
              $first_id = $rows->fields['faq_categories_id'];
            }
            $last_id = $rows->fields['faq_categories_id'];
            $rows->MoveNext();
          }
          $this->tree[$last_id]['next_id'] = $this->tree[$value]['next_id'];
          $this->tree[$value]['next_id'] = $first_id;
          $new_path .= '_';
        } else {
          break;
        }
      }
    }
    $row = 0;
    return $this->zen_show_faq_category($first_element, $row);
  }
  function zen_show_faq_category($counter,$ii) {
    global $fcPath_array;
    $this->faq_categories_string = "";
      $fcPath_new = 'fcPath=' . $counter;
      $this->box_faq_categories_array[$ii]['top'] = 'true';

    $this->box_faq_categories_array[$ii]['path'] = $fcPath_new;
    if (isset($fcPath_array) && in_array($counter, $fcPath_array)) {
      $this->box_faq_categories_array[$ii]['current'] = true;
    } else {
      $this->box_faq_categories_array[$ii]['current'] = false;
    }
    // display faq_category name
    $this->box_faq_categories_array[$ii]['name'] = $this->faq_categories_string . $this->tree[$counter]['name'];
      $this->box_faq_categories_array[$ii]['has_sub_cat'] = false;
    if (SHOW_FAQ_COUNTS == 'true') {
      $faqs_in_faq_category = zen_count_faqs_in_faq_category($counter);
      if ($faqs_in_faq_category > 0) {
        $this->box_faq_categories_array[$ii]['count'] = $faqs_in_faq_category;
      } else {
        $this->box_faq_categories_array[$ii]['count'] = 0;
      }
    }
    if ($this->tree[$counter]['next_id'] != false) {
      $ii++;
      $this->zen_show_faq_category($this->tree[$counter]['next_id'], $ii);
    }
    return $this->box_faq_categories_array;
  }
}
?>