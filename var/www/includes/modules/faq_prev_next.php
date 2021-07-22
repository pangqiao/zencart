<?php
/**
 * @package FAQ Manager
 * @copyright Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * based on @version $Id: product_prev_next.php 6912 2007-09-02 02:23:45Z drbyte $
 * @Original contrib by Vijay Immanuel for osCommerce, converted to zen by dave@open-operations.com - http://www.open-operations.com
 * @faq_prev_next.php updated 2012-09-18 to be v1.5 compatible kamelion0927
 */
if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}

// bof: previous next
if (FAQ_INFO_PREVIOUS_NEXT_STATUS != 0) {

  // sort order
  switch(FAQ_INFO_PREVIOUS_NEXT_SORT) {
    case (0):
    $prev_next_order= ' order by LPAD(p.faqs_id,11,"0")';
    break;
    case (1):
    $prev_next_order= " order by pd.faqs_name";
    break;
    case (2):
    $prev_next_order= " order by pd.faqs_name DESC";
    break;
    case (3):
    $prev_next_order= " order by pc.sort_order, p.faqs_sort_order";
    break;
    case (4):
    $prev_next_order= " order by p.faqs_sort_order";
    break;
    case (5):
    $prev_next_order= " order by p.faqs_date_added DESC, pd.faqs_name";
    break;
    case (6):
    $prev_next_order= " order by p.faqs_date_added, pd.faqs_name";
    break;
    default:
    $prev_next_order= " order by p.faqs_sort_order";
    break;
  }

  if ($fcPath < 1) {
    $fcPath = zen_get_faq_path((int)$_GET['faqs_id']);
    $fcPath_array = zen_parse_faq_category_path($fcPath);
    $fcPath = implode('_', $fcPath_array);
    $current_faq_category_id = $fcPath_array[(sizeof($fcPath_array)-1)];
  }


  $sql = "select p.faqs_id, pd.faqs_name, p.faqs_sort_order
          from   " . TABLE_FAQS . " p, "
  . TABLE_FAQS_DESCRIPTION . " pd, "
  . TABLE_FAQS_TO_FAQ_CATEGORIES . " ptc
          where  p.faqs_status = '1' and p.faqs_id = pd.faqs_id and pd.language_id= '" . (int)$_SESSION['languages_id'] . "' and p.faqs_id = ptc.faqs_id and ptc.faq_categories_id = '" . (int)$current_faq_category_id . "'" .
  $prev_next_order;

  $faqs_ids = $db->Execute($sql);
  $faqs_found_count = $faqs_ids->RecordCount();

  while (!$faqs_ids->EOF) {
    $id_array[] = $faqs_ids->fields['faqs_id'];
    $faqs_ids->MoveNext();
  }

  // if invalid faq id skip
  if (is_array($id_array)) {
    reset ($id_array);
    $counter = 0;
    foreach ($id_array as $key => $value) {
      if ($value == (int)$_GET['faqs_id']) {
        $position = $counter;
        if ($key == 0) {
          $previous = -1; // it was the first to be found
        } else {
          $previous = $id_array[$key - 1];
        }
        if (isset($id_array[$key + 1]) && $id_array[$key + 1]) {
          $next_item = $id_array[$key + 1];
        } else {
          $next_item = $id_array[0];
        }
      }
      $last = $value;
      $counter++;
    }

    if ($previous == -1) $previous = $last;

    $sql = "select faq_categories_name
            from   " . TABLE_FAQ_CATEGORIES_DESCRIPTION . "
            where  faq_categories_id = " . (int)$current_faq_category_id . " AND language_id = '" . (int)$_SESSION['languages_id'] . "'";

    $category_name_row = $db->Execute($sql);
  } // if is_array

  // previous_next button settings
  $previous_button = zen_image_button(BUTTON_IMAGE_FAQ_PREVIOUS, BUTTON_FAQ_PREVIOUS_ALT);
  $next_item_button = zen_image_button(BUTTON_IMAGE_FAQ_NEXT, BUTTON_FAQ_NEXT_ALT);
}
// eof: previous next
?>