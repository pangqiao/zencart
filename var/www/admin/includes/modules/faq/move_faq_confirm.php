<?php
/**
 * @package FAQ Manager
 * @copyright Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * based on @version $Id: move_product_confirm.php 3009 2006-02-11 15:41:10Z wilt $
 * @Original contrib by Vijay Immanuel for osCommerce, converted to zen by dave@open-operations.com - http://www.open-operations.com
 * @move_faq_confirm.php updated 2012-09-18 to be v1.5 compatible kamelion0927
 */
if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}
        $faqs_id = zen_db_prepare_input($_POST['faqs_id']);
        $new_parent_id = zen_db_prepare_input($_POST['move_to_faq_category_id']);

        $duplicate_check = $db->Execute("select count(*) as total
                                        from " . TABLE_FAQS_TO_FAQ_CATEGORIES . "
                                        where faqs_id = '" . (int)$faqs_id . "'
                                        and faq_categories_id = '" . (int)$new_parent_id . "'");

        if ($duplicate_check->fields['total'] < 1) {
          $db->Execute("update " . TABLE_FAQS_TO_FAQ_CATEGORIES . "
                        set faq_categories_id = '" . (int)$new_parent_id . "'
                        where faqs_id = '" . (int)$faqs_id . "'
                        and faq_categories_id = '" . (int)$current_faq_category_id . "'");

          // reset master_faq_categories_id if moved from original master faq_category
          $check_master = $db->Execute("select faqs_id, master_faq_categories_id from " . TABLE_FAQS . " where faqs_id='" .  (int)$faqs_id . "'");
          if ($check_master->fields['master_faq_categories_id'] == (int)$current_faq_category_id) {
            $db->Execute("update " . TABLE_FAQS . "
                          set master_faq_categories_id='" . (int)$new_parent_id . "'
                          where faqs_id = '" . (int)$faqs_id . "'");
          }
        } else {
          $messageStack->add_session(ERROR_CANNOT_MOVE_FAQ_TO_FAQ_CATEGORY_SELF, 'error');
        }

        zen_redirect(zen_href_link(FILENAME_FAQ_CATEGORIES, 'fcPath=' . $new_parent_id . '&pID=' . $faqs_id . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '')));
?>