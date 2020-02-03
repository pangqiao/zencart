<?php
/**
 * @package FAQ Manager
 * @copyright Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * based on @version $Id: products.php 4265 2006-08-25 08:09:36Z drbyte $
 * @Original contrib by Vijay Immanuel for osCommerce, converted to zen by dave@open-operations.com - http://www.open-operations.com
 * @faqs.php updated 2012-09-18 to be v1.5 compatible kamelion0927
 */
if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}
  class faqs {
    var $modules, $selected_module;

// class constructor
    function faqs($module = '') {
    }

    function get_faqs_in_faq_category($zf_faq_category_id, $zf_recurse=true, $zf_faq_ids_only=false) {
      global $db;
      $za_faqs_array = array();
      // get top level faqs
      $zp_faqs_query = "select ptc.*, pd.faqs_name
                            from " . TABLE_FAQS_TO_FAQ_CATEGORIES . " ptc
                            left join " . TABLE_FAQS_DESCRIPTION . " pd
                            on ptc.faqs_id = pd.faqs_id
                            and pd.language_id = '" . (int)$_SESSION['languages_id'] . "'
                            where ptc.faq_categories_id='" . $zf_faq_category_id . "'
                            order by pd.faqs_name";

      $zp_faqs = $db->Execute($zp_faqs_query);
      while (!$zp_faqs->EOF) {
        if ($zf_faq_ids_only) {
          $za_faqs_array[] = $zp_faqs->fields['faqs_id'];
        } else {
          $za_faqs_array[] = array('id' => $zp_faqs->fields['faqs_id'],
                                       'text' => $zp_faqs->fields['faqs_name']);
        }
        $zp_faqs->MoveNext();
      }
      if ($zf_recurse) {
        $zp_faq_categories_query = "select faq_categories_id from " . TABLE_FAQ_CATEGORIES . "
                                where parent_id = '"   . $zf_faq_category_id . "'";
        $zp_faq_categories = $db->Execute($zp_faq_categories_query);
        while (!$zp_faq_categories->EOF) {
          $za_sub_faqs_array = $this->get_faqs_in_faq_category($zp_faq_categories->fields['faq_categories_id'], true, $zf_faq_ids_only);
          $za_faqs_array = array_merge($za_faqs_array, $za_sub_faqs_array);
          $zp_faq_categories->MoveNext();
        }
      }
      return $za_faqs_array;
    }

    function faqs_name($zf_faq_id) {
      global $db;
      $zp_faq_name_query = "select faqs_name from " . TABLE_FAQS_DESCRIPTION . "
                                where language_id = '" . $_SESSION['languages_id'] . "'
                                and faqs_id = '" . (int)$zf_faq_id . "'";
      $zp_faq_name = $db->Execute($zp_faq_name_query);
      $zp_faq_name = $zp_faq_name->fields['faqs_name'];
      return $zp_faq_name;
    }
?>