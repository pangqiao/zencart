<?php
/**
 * @package FAQ Manager
 * @copyright Copyright 2003-2009 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * based on @version $Id: copy_to_confirm.php 14139 2009-08-10 13:46:02Z wilt $
 * @Original contrib by Vijay Immanuel for osCommerce, converted to zen by dave@open-operations.com - http://www.open-operations.com
 * @copy_to_confirm.php updated 2012-09-18 to be v1.5 compatible kamelion0927
 */


if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}
        if (isset($_POST['faqs_id']) && isset($_POST['faq_categories_id'])) {
          $faqs_id = zen_db_prepare_input($_POST['faqs_id']);
          $faq_categories_id = zen_db_prepare_input($_POST['faq_categories_id']);

          $faqs_id_from=$faqs_id;

          if ($_POST['copy_as'] == 'link') {
            if ($faq_categories_id != $current_faq_category_id) {
              $check = $db->Execute("select count(*) as total
                                     from " . TABLE_FAQS_TO_FAQ_CATEGORIES . "
                                     where faqs_id = '" . (int)$faqs_id . "'
                                     and faq_categories_id = '" . (int)$faq_categories_id . "'");
              if ($check->fields['total'] < '1') {
                $db->Execute("insert into " . TABLE_FAQS_TO_FAQ_CATEGORIES . "
                                          (faqs_id, faq_categories_id)
                              values ('" . (int)$faqs_id . "', '" . (int)$faq_categories_id . "')");
              }
            } else {
              $messageStack->add_session(ERROR_CANNOT_LINK_TO_SAME_FAQ_CATEGORY, 'error');
            }
          } elseif ($_POST['copy_as'] == 'duplicate') {
            $old_faqs_id = (int)$faqs_id;
            $faq = $db->Execute("select *
                                     from " . TABLE_FAQS . "
                                     where faqs_id = '" . (int)$faqs_id . "'");

            $db->Execute("insert into " . TABLE_FAQS . "
                                      (faqs_type, faqs_date_added, faqs_status,
									   faqs_sort_order, master_faq_categories_id
                                       )
                          values ('" . zen_db_input($faq->fields['faqs_type']) . "',
                                  now(),
                                  '" . zen_db_input($faq->fields['faqs_sort_order']) . "',
                                  '" . zen_db_input($faq_categories_id) .
                                  "')");

            $dup_faqs_id = $db->Insert_ID();

            $description = $db->Execute("select language_id, faqs_name
                                         from " . TABLE_FAQS_DESCRIPTION . "
                                         where faqs_id = '" . (int)$faqs_id . "'");
            while (!$description->EOF) {
              $db->Execute("insert into " . TABLE_FAQS_DESCRIPTION . "
                                        (faqs_id, language_id, faqs_name)
                            values ('" . (int)$dup_faqs_id . "',
                                    '" . (int)$description->fields['language_id'] . "',
                                    '" . zen_db_input($description->fields['faqs_name']) . "'");
              $description->MoveNext();
            }

            $db->Execute("insert into " . TABLE_FAQS_TO_FAQ_CATEGORIES . "
                                      (faqs_id, faq_categories_id)
                          values ('" . (int)$dup_faqs_id . "', '" . (int)$faq_categories_id . "')");
            $faqs_id = $dup_faqs_id;
            $description->MoveNext();
          }
        }
        zen_redirect(zen_href_link(FILENAME_FAQ_CATEGORIES, 'fcPath=' . $faq_categories_id . '&pID=' . $faqs_id . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '')));
?>