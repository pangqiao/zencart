<?php
/**
 * @package FAQ Manager
 * @copyright Copyright 2003-2011 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * based on @version $Id: update_product.php 18695 2011-05-04 05:24:19Z drbyte $
 * @Original contrib by Vijay Immanuel for osCommerce, converted to zen by dave@open-operations.com - http://www.open-operations.com
 * @update_faq.php updated 2012-09-18 to be v1.5 compatible kamelion0927
 */
if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}
    if (zen_not_null($_POST)) {
      $pInfo = new objectInfo($_POST);
      $faqs_name = $_POST['faqs_name'];
      $faqs_answer = $_POST['faqs_answer'];
    } else {
      $faqs = $db->Execute("select p.faqs_id, pd.language_id, pd.faqs_name, 
                            pd.faqs_answer, p.faqs_date_added, p.faqs_last_modified,
                            p.faqs_status, p.faqs_sort_order
                            from " . TABLE_FAQS . " p, " . TABLE_FAQS_DESCRIPTION . " pd
                            where p.faqs_id = pd.faqs_id
                            and p.faqs_id = '" . (int)$_GET['pID'] . "'");

      $pInfo = new objectInfo($faq->fields);
    }
	$languages = zen_get_languages();
    for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
    $pInfo->faqs_name = zen_db_prepare_input($faqs_name[$languages[$i]['id']]);
	$pInfo->faqs_answer = zen_db_prepare_input($faqs_answer[$languages[$i]['id']]);
    }
		
		if (isset($_GET['pID'])) $faqs_id = zen_db_prepare_input($_GET['pID']);
		$sql_data_array = array('faqs_status' => zen_db_prepare_input((int)$_POST['faqs_status']),
		                        'faqs_sort_order' => (int)zen_db_prepare_input($_POST['faqs_sort_order'])
								);
		
          if ($action == 'insert_faq') {
            $insert_sql_data = array( 'faqs_date_added' => 'now()',
                                      'master_faq_categories_id' => (int)$current_faq_category_id);

            $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

            zen_db_perform(TABLE_FAQS, $sql_data_array);
            $faqs_id = zen_db_insert_id();

            $db->Execute("insert into " . TABLE_FAQS_TO_FAQ_CATEGORIES . "
                                      (faqs_id, faq_categories_id)
                          values ('" . (int)$faqs_id . "', '" . (int)$current_faq_category_id . "')");
          } elseif ($action == 'update_faq') {
            $update_sql_data = array( 'faqs_last_modified' => 'now()',
                                      'master_faq_categories_id' => ($_POST['master_faq_category'] > 0 ? zen_db_prepare_input($_POST['master_faq_category']) : zen_db_prepare_input($_POST['master_faq_categories_id'])));

            $sql_data_array = array_merge($sql_data_array, $update_sql_data);

            zen_db_perform(TABLE_FAQS, $sql_data_array, 'update', "faqs_id = '" . (int)$faqs_id . "'");
          }

          $languages = zen_get_languages();
          for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
            $language_id = $languages[$i]['id'];

            $sql_data_array = array('faqs_name' => zen_db_prepare_input($_POST['faqs_name'][$language_id]),
									'faqs_answer' => zen_db_prepare_input($_POST['faqs_answer'][$language_id]));

            if ($action == 'insert_faq') {
              $insert_sql_data = array('faqs_id' => $faqs_id,
                                       'language_id' => $language_id);

              $sql_data_array = array_merge($sql_data_array, $insert_sql_data);

              zen_db_perform(TABLE_FAQS_DESCRIPTION, $sql_data_array);
            } elseif ($action == 'update_faq') {
              zen_db_perform(TABLE_FAQS_DESCRIPTION, $sql_data_array, 'update', "faqs_id = '" . (int)$faqs_id . "' and language_id = '" . (int)$language_id . "'");
            }
          }

          zen_redirect(zen_href_link(FILENAME_FAQ_CATEGORIES, 'fcPath=' . $fcPath . '&pID=' . $faqs_id . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '')));
?>