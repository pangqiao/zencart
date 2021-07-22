<?php
/**
 * @package FAQ Manager
 * @copyright Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * based on @version $Id: sidebox_delete_product.php 3358 2006-04-03 04:33:32Z ajeh $
 * @Original contrib by Vijay Immanuel for osCommerce, converted to zen by dave@open-operations.com - http://www.open-operations.com
 * @sidebox_delete_faq.php updated 2012-09-18 to be v1.5 compatible kamelion0927
 */
if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}

        $heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_FAQ . '</b>');
		$type_faq_admin_handler = faq;
        $contents = array('form' => zen_draw_form('faqs', $type_faq_admin_handler, 'action=delete_faq_confirm&fcPath=' . $fcPath . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '')) . zen_draw_hidden_field('faqs_id', $pInfo->faqs_id));
        $contents[] = array('text' => TEXT_DELETE_FAQ_INTRO);
        $contents[] = array('text' => '<br /><b>Name:&nbsp;' . $pInfo->faqs_name . '<br/>ID#&nbsp;' . $pInfo->faqs_id . '</b>');

        $faq_faq_categories_string = '';
        $faq_faq_categories = zen_generate_faq_category_path($pInfo->faqs_id, 'faq');
        if (sizeof($faq_faq_categories) > 1) {
          $contents[] = array('text' => '<br /><b><span class="alert">' . TEXT_MASTER_FAQ_CATEGORIES_ID . '</span>' . '</b>');
        }
        for ($i = 0, $n = sizeof($faq_faq_categories); $i < $n; $i++) {
          $faq_category_path = '';
          for ($j = 0, $k = sizeof($faq_faq_categories[$i]); $j < $k; $j++) {
            $faq_category_path .= $faq_faq_categories[$i][$j]['text'] . '&nbsp;&gt;&nbsp;';
          }
          $faq_category_path = substr($faq_category_path, 0, -16);
          if (sizeof($faq_faq_categories) > 1 && zen_get_parent_faq_category_id($pInfo->faqs_id) == $faq_faq_categories[$i][sizeof($faq_faq_categories[$i])-1]['id']) {
            $faq_faq_categories_string .= '<strong><span class="alert">' . zen_draw_checkbox_field('faq_faq_categories[]', $faq_faq_categories[$i][sizeof($faq_faq_categories[$i])-1]['id'], true) . '&nbsp;' . $faq_category_path . '</strong></span><br />';
          } else {
          $faq_faq_categories_string .= zen_draw_checkbox_field('faq_faq_categories[]', $faq_faq_categories[$i][sizeof($faq_faq_categories[$i])-1]['id'], true) . '&nbsp;' . $faq_category_path . '<br />';
          }
        }
        $faq_faq_categories_string = substr($faq_faq_categories_string, 0, -4);

        $contents[] = array('text' => '<br />' . $faq_faq_categories_string);
        $contents[] = array('align' => 'center', 'text' => '<br />' . zen_image_submit('button_delete.gif', IMAGE_DELETE) . ' <a href="' . zen_href_link(FILENAME_FAQ_CATEGORIES, 'fcPath=' . $fcPath . '&pID=' . $pInfo->faqs_id . (isset($_GET['page']) ? '&page=' . $_GET['page'] : '')) . '">' . zen_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
?>