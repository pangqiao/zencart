<?php
/**
 * @package FAQ Manager
 * @copyright Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * based on @version $Id: delete_product_confirm.php 3345 2006-04-02 05:57:34Z drbyte $
 * @Original contrib by Vijay Immanuel for osCommerce, converted to zen by dave@open-operations.com - http://www.open-operations.com
 * @delete_faq_confirm.php updated 2012-09-18 to be v1.5 compatible kamelion0927
 */
  if (!defined('IS_ADMIN_FLAG')) {
    die('Illegal Access');
  }
  $do_delete_flag = false;
  if (isset($_POST['faqs_id']) && isset($_POST['faq_faq_categories']) && is_array($_POST['faq_faq_categories'])) {
    $faq_id = zen_db_prepare_input($_POST['faqs_id']);
    $faq_faq_categories = $_POST['faq_faq_categories'];
    $do_delete_flag = true;
    if (!isset($delete_linked)) $delete_linked = 'true';
  }

  if (zen_not_null($cascaded_faq_id_for_delete) && zen_not_null($cascaded_faq_cat_for_delete) ) {
    $faq_id = $cascaded_faq_id_for_delete;
    $faq_faq_categories = $cascaded_faq_cat_for_delete;
    $do_delete_flag = true;
    // no check for $delete_linked here, because it should already be passed from faq_categories.php
  }
  if ($do_delete_flag) {
      for ($i=0, $n=sizeof($faq_faq_categories); $i<$n; $i++) {
      $db->Execute("delete from " . TABLE_FAQS_TO_FAQ_CATEGORIES . "
                    where faqs_id = '" . (int)$faq_id . "'
                    and faq_categories_id = '" . (int)$faq_faq_categories[$i] . "'");
    }
    // confirm that faq is no longer linked to any categories
    $faq_faq_categories = $db->Execute("select count(*) as total
                                      from " . TABLE_FAQS_TO_FAQ_CATEGORIES . "
                                      where faqs_id = '" . (int)$faq_id . "'");

    // if not linked to any categories, do delete:
    if ($faq_faq_categories->fields['total'] == '0') {
      zen_remove_faq($faq_id, $delete_linked);
    }
    }

  // if this is a single-faq delete, redirect to faq categories page
  // if not, then this file was called by the cascading delete initiated by the faq category-delete process
  if ($action == 'delete_faq_confirm') zen_redirect(zen_href_link(FILENAME_FAQ_CATEGORIES, 'fcPath=' . $fcPath));
?>